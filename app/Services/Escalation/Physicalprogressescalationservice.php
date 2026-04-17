<?php

namespace App\Services\Escalation;

use App\Models\SubPackageProject;
use App\Models\PhysicalBoqProgress;
use App\Models\EscalationLog;
use App\Services\Escalation\BaseEscalationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * PHYSICAL PROGRESS ESCALATION SERVICE
 *
 * Violation Logic:
 *   A SubPackageProject is in violation when:
 *   1. Its contract has started (commencement_date exists).
 *   2. No physical progress (BOQ or EPC) has been submitted within the last
 *      EXPECTED_INTERVAL_DAYS days since the contract started or the last entry.
 *
 * Days Violated = days since last physical progress entry
 *                 (or since commencement_date if no entry exists at all).
 */
class PhysicalProgressEscalationService extends BaseEscalationService
{
    /**
     * How many days without an entry before we consider it a violation.
     * Set to 0 so ANY gap beyond the matrix thresholds is caught.
     */
    const GRACE_PERIOD_DAYS = 0;

    /**
     * Evaluate one SubPackageProject for physical progress violation.
     */
    public function processSubProject(SubPackageProject $subProject, $console = null): void
    {
        $label = "[PhysicalProgress] SubProject #{$subProject->id} ({$subProject->name})";

        $daysViolated = $this->detectViolation($subProject, $console);

        if ($daysViolated === null) {
            if ($console) $console->line("  🟢 {$label} — No violation.");
            return;
        }

        if ($console) $console->warn("  ⚠️ {$label} — Violation! Days since last entry: {$daysViolated}");

        $this->triggerEscalation(
            $subProject,
            EscalationLog::CATEGORY_PHYSICAL,
            $daysViolated,
            null,   // no compliance_id for physical
            $console
        );
    }

    /**
     * Detect violation: returns days since last physical entry, or null if no violation.
     */
    public function detectViolation(SubPackageProject $subProject, $console = null): ?int
    {
        // 1. Get the active contract for this sub-project
        $contract = $subProject->packageProject?->contracts()->latest('id')->first();

        if (!$contract || !$contract->commencement_date) {
            if ($console) $console->line('  - Skip: No contract or commencement date found.');
            return null;
        }

        $commencementDate = Carbon::parse($contract->commencement_date)->startOfDay();

        // 2. If contract hasn't started yet, skip
        if ($commencementDate->isFuture()) {
            if ($console) $console->line('  - Skip: Contract has not commenced yet.');
            return null;
        }

        // 3. Check if contract is already completed
        $completionDate = $contract->actual_completion_date
            ?? $contract->revised_completion_date
            ?? $contract->initial_completion_date;

        if ($completionDate && Carbon::parse($completionDate)->isPast()) {
            if ($console) $console->line('  - Skip: Contract is already completed.');
            return null;
        }

        // 4. Find the most recent BOQ or EPC physical progress entry
        $lastBoqEntry = DB::table('physical_boq_progress')
            ->where('sub_package_project_id', $subProject->id)
            ->whereNull('deleted_at')
            ->orderByDesc('progress_submitted_date')
            ->value('progress_submitted_date');

        $lastEpcEntry = DB::table('physical_epc_progress')
            ->where('sub_package_project_id', $subProject->id)
            ->whereNull('deleted_at')
            ->orderByDesc('progress_submitted_date')
            ->value('progress_submitted_date');

        // Use whichever is more recent
        $lastEntryDate = null;
        if ($lastBoqEntry || $lastEpcEntry) {
            $dates = array_filter([$lastBoqEntry, $lastEpcEntry]);
            $lastEntryDate = Carbon::parse(max($dates))->startOfDay();
        }

        // 5. Calculate days without progress
        $referenceDate = $lastEntryDate ?? $commencementDate;
        $daysElapsed   = (int) $referenceDate->diffInDays(now()->startOfDay());

        if ($console) {
            $since = $lastEntryDate ? $lastEntryDate->toDateString() : "commencement ({$commencementDate->toDateString()})";
            $console->line("  - Last physical entry: {$since} ({$daysElapsed} days ago)");
        }

        // 6. Apply grace period — only flag if beyond threshold
        if ($daysElapsed <= self::GRACE_PERIOD_DAYS) {
            return null;
        }

        // 7. Only flag if the matrix actually has a rule for this duration
        $rules = EscalationMatrix::getPhysicalRules();
        if (EscalationMatrix::findApplicableDayMark($rules, $daysElapsed) === null) {
            return null;
        }

        return $daysElapsed;
    }
}