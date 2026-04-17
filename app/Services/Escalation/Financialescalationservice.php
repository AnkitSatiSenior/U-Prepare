<?php

namespace App\Services\Escalation;

use App\Models\SubPackageProject;
use App\Models\EscalationLog;
use App\Services\Escalation\BaseEscalationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * FINANCIAL PROGRESS ESCALATION SERVICE
 *
 * Violation Logic:
 *   A SubPackageProject is in violation when:
 *   1. Its contract has commenced (commencement_date exists and is in the past).
 *   2. No financial bill (financial_progress_updates) has been submitted within
 *      the last BILLING_INTERVAL_DAYS days since contract start or last submission.
 *
 * Days Violated = days since last financial submission
 *                 (or since commencement_date if no submission has ever been made).
 */
class FinancialEscalationService extends BaseEscalationService
{
    /**
     * The minimum expected billing interval in days.
     * Only gaps LONGER than this are treated as violations.
     * Default: 0 (any gap crossing the matrix threshold is caught).
     */
    const MIN_BILLING_INTERVAL_DAYS = 0;

    public function processSubProject(SubPackageProject $subProject, $console = null): void
    {
        $label = "[Financial] SubProject #{$subProject->id} ({$subProject->name})";

        $daysViolated = $this->detectViolation($subProject, $console);

        if ($daysViolated === null) {
            if ($console) $console->line("  🟢 {$label} — No violation.");
            return;
        }

        if ($console) $console->warn("  ⚠️ {$label} — Violation! Days since last billing: {$daysViolated}");

        $this->triggerEscalation(
            $subProject,
            EscalationLog::CATEGORY_FINANCIAL,
            $daysViolated,
            null,   // no compliance_id for financial
            $console
        );
    }

    /**
     * Detect violation: returns days since last financial submission, or null if no violation.
     */
    public function detectViolation(SubPackageProject $subProject, $console = null): ?int
    {
        // 1. Get active contract
        $contract = $subProject->packageProject?->contracts()->latest('id')->first();

        if (!$contract || !$contract->commencement_date) {
            if ($console) $console->line('  - Skip [Financial]: No contract or commencement date.');
            return null;
        }

        $commencementDate = Carbon::parse($contract->commencement_date)->startOfDay();

        // 2. Contract must have started
        if ($commencementDate->isFuture()) {
            if ($console) $console->line('  - Skip [Financial]: Contract has not commenced yet.');
            return null;
        }

        // 3. Skip if contract is completed
        $completionDate = $contract->actual_completion_date
            ?? $contract->revised_completion_date
            ?? $contract->initial_completion_date;

        if ($completionDate && Carbon::parse($completionDate)->isPast()) {
            if ($console) $console->line('  - Skip [Financial]: Contract is already completed.');
            return null;
        }

        // 4. Find the most recent financial bill submission for this sub-project
        $lastSubmission = DB::table('financial_progress_updates')
            ->where('project_id', $subProject->id)
            ->whereNull('deleted_at')
            ->orderByDesc('submit_date')
            ->value('submit_date');

        $referenceDate = $lastSubmission
            ? Carbon::parse($lastSubmission)->startOfDay()
            : $commencementDate;

        $daysElapsed = (int) $referenceDate->diffInDays(now()->startOfDay());

        if ($console) {
            $since = $lastSubmission
                ? Carbon::parse($lastSubmission)->toDateString()
                : "commencement ({$commencementDate->toDateString()})";
            $console->line("  - Last financial entry: {$since} ({$daysElapsed} days ago)");
        }

        // 5. Check matrix — only flag if a rule actually applies
        $rules = EscalationMatrix::getFinancialRules();
        if (EscalationMatrix::findApplicableDayMark($rules, $daysElapsed) === null) {
            return null;
        }

        return $daysElapsed;
    }
}