<?php

namespace App\Helpers;

use Carbon\Carbon;

class MilestoneHelper
{
    /*
    |--------------------------------------------------------------------------
    | Generate Standard Milestones with Progress
    |--------------------------------------------------------------------------
    */
    public static function generateWithProgress($contract, $subProjectsData): array
    {
        if (!$contract->commencement_date || !$contract->initial_completion_date) {
            return []; // must have start and initial end
        }

        $startDate = Carbon::parse($contract->commencement_date)->startOfDay();
        $initialEnd = Carbon::parse($contract->initial_completion_date)->endOfDay();
        $newEndDate = $contract->initial_completion_date && Carbon::parse($contract->initial_completion_date)->gt($initialEnd) ? Carbon::parse($contract->initial_completion_date)->endOfDay() : $initialEnd;

        $totalDays = max(1, $startDate->diffInDays($initialEnd) + 1); // at least 1 day

        $splitsFinance = config('milestones.default_finance_split', [20, 30, 50]);
        $splitsPhysical = config('milestones.default_physical_split', [20, 30, 50]);

        // Force milestones even if oldEnd < startDate
        if ($initialEnd->lt($startDate)) {
            $initialEnd = $startDate->copy()->addMonth()->endOfDay(); // fallback 1 month
        }

        return self::buildMilestones($subProjectsData, $startDate, $initialEnd, $newEndDate, $splitsFinance, $splitsPhysical);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Amended Milestones (for revised contracts)
    |--------------------------------------------------------------------------
    */
    public static function generateAmendedWithProgress($contract, $subProjectsData): array
    {
        if (!$contract->commencement_date || !$contract->initial_completion_date) {
            return [];
        }

        $startDate = Carbon::parse($contract->commencement_date)->startOfDay();

        // Latest amendment (if exists)
        $latestUpdate = $contract->updates()->latest('changed_at')->first();

        // Old End Date (pre-extension)
        if ($latestUpdate && $latestUpdate->old_initial_completion_date) {
            $oldEndDate = Carbon::parse($latestUpdate->old_initial_completion_date)->endOfDay();
        } else {
            $oldEndDate = Carbon::parse($contract->initial_completion_date)->endOfDay();
        }

        // New End Date (after extension)
        if ($latestUpdate && $latestUpdate->new_initial_completion_date) {
            $newEndDate = Carbon::parse($latestUpdate->new_initial_completion_date)->endOfDay();
        } elseif ($contract->revised_completion_date) {
            $newEndDate = Carbon::parse($contract->revised_completion_date)->endOfDay();
        } else {
            $newEndDate = Carbon::parse($contract->initial_completion_date)->endOfDay();
        }

        // Ensure correct ordering
        if ($oldEndDate->lt($startDate)) {
            $oldEndDate = $startDate->copy()->addMonths(6);
        }
        if ($newEndDate->lt($oldEndDate)) {
            $newEndDate = $oldEndDate->copy();
        }

        $splitsFinance = config('milestones.default_finance_split', [20, 30, 50]);
        $splitsPhysical = config('milestones.default_physical_split', [20, 30, 50]);

        return self::buildMilestones($subProjectsData, $startDate, $oldEndDate, $newEndDate, $splitsFinance, $splitsPhysical);
    }

    /*
    |--------------------------------------------------------------------------
    | Private: Build Milestones (day-accurate split)
    |--------------------------------------------------------------------------
    */
    private static function buildMilestones($subProjectsData, Carbon $startDate, Carbon $oldEndDate, Carbon $newEndDate, array $splitsFinance, array $splitsPhysical): array
    {
        $start = $startDate->copy()->startOfDay();
        $oldEnd = $oldEndDate->copy()->endOfDay();
        $newEnd = $newEndDate->copy()->endOfDay();

        // Force 3 milestones
        $count = 3;

        // Normalize split percentages
        $splitsF = array_values(array_pad($splitsFinance, $count, 0));
        $splitsP = array_values(array_pad($splitsPhysical, $count, 0));

        // Total calendar months (inclusive)
        $totalMonths = $start->diffInMonths($oldEnd) + 1;

        // Equal months per milestone
        $monthsPerMilestone = intdiv($totalMonths, $count);
        $extraMonths = $totalMonths % $count;

        /**
         * Example:
         * totalMonths = 18
         * → 18 / 3 = 6 each
         * → extra = 0
         */

        $milestones = [];
        $currentFrom = $start->copy();

        for ($i = 0; $i < $count; $i++) {
            // Distribute leftover months (rare case)
            $blockMonths = $monthsPerMilestone + ($i < $extraMonths ? 1 : 0);

            // From date
            $from = $currentFrom->copy();

            // To date = from + X months - 1 day
            $to = $from->copy()->addMonths($blockMonths)->subDay()->endOfDay();

            // Extend last milestone up to new contract end
            if ($i === $count - 1 && $newEnd->gt($oldEnd)) {
                $to = $newEnd->copy()->endOfDay();
            }

            // Final month count (always whole number)
            $monthCount = $blockMonths; // always whole number

            $milestones[] = [
                'label' => $i === $count - 1 && $newEnd->gt($oldEnd) ? 'Extended M' . ($i + 1) : 'M' . ($i + 1),

                'from' => $from,
                'to' => $to,
                'months' => $monthCount,
                'days' => $from->diffInDays($to) + 1,

                'plannedFinance' => $splitsF[$i],
                'plannedPhysical' => $splitsP[$i],

                'achievedFinance' => self::calculateMilestoneProgress($subProjectsData, $from, $to, 'finance'),

                'achievedPhysical' => self::calculateMilestoneProgress($subProjectsData, $from, $to, 'physical'),
            ];

            // Next block starts next day
            $currentFrom = $to->copy()->addDay()->startOfDay();
        }

        return $milestones;
    }

    /*
    |--------------------------------------------------------------------------
    | Private: Calculate Achieved Progress
    |--------------------------------------------------------------------------
    */
    private static function calculateMilestoneProgress($subProjectsData, Carbon $from, Carbon $to, string $type = 'finance'): float
    {
        $totalContract = collect($subProjectsData)->sum('contractValue');
        if ($totalContract <= 0) {
            return 0.0;
        }

        $totalAmount = 0;

        foreach ($subProjectsData as $sp) {
            $entries = $type === 'finance' ? $sp['financeEntries'] ?? ($sp['financeSubmissions'] ?? []) : $sp['physicalEntries'] ?? ($sp['physicalSubmissions'] ?? []);

            foreach ($entries as $entry) {
                $date = isset($entry['submit_date']) ? Carbon::parse($entry['submit_date']) : (isset($entry['date']) ? Carbon::parse($entry['date']) : null);

                if ($date && $date->between($from, $to)) {
                    if ($type === 'finance') {
                        $totalAmount += $entry['amount'] ?? ($entry['finance_amount'] ?? 0);
                    } else {
                        $totalAmount += isset($entry['percent']) ? ($entry['percent'] / 100) * ($sp['contractValue'] ?? 0) : $entry['amount'] ?? 0;
                    }
                }
            }
        }

        return round(($totalAmount / $totalContract) * 100, 2);
    }
}
