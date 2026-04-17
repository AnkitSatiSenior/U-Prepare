<?php

namespace App\Services\Escalation;

use App\Models\ContractSecurity;
use App\Models\EscalationLog;
use App\Services\Escalation\BaseEscalationService;
use Carbon\Carbon;

/**
 * SECURITY ESCALATION SERVICE
 *
 * Violation Logic:
 *   A ContractSecurity is in violation when it is within 30 days of expiry
 *   OR has already expired.
 *
 * Days Violated Calculation:
 *   The "30-day warning window" is the trigger point.
 *   days_violated = 30 - days_remaining
 *
 *   Examples:
 *     25 days remaining  → days_violated = 5   (entered warning zone 5 days ago)
 *     15 days remaining  → days_violated = 15
 *     0  days remaining  → days_violated = 30  (expiry day)
 *     Expired 10 days ago → days_violated = 40 (30 + 10)
 *
 * The EscalationMatrix security rules are keyed on these days_violated values,
 * creating a smooth escalation ladder from first warning all the way to post-expiry.
 */
class SecurityEscalationService extends BaseEscalationService
{
    /**
     * Warning window: how many days before expiry to start escalation.
     */
    const WARNING_WINDOW_DAYS = 30;

    /**
     * Process all ContractSecurity records system-wide.
     * Called by the master EscalationService orchestrator.
     */
    public function processAllSecurities($console = null): void
    {
        $securities = ContractSecurity::with(['contract.project', 'type'])->get();

        if ($console) $console->info("[Security] Evaluating {$securities->count()} contract securities...");

        foreach ($securities as $security) {
            $this->processSecurity($security, $console);
        }
    }

    /**
     * Process a single ContractSecurity record.
     */
    public function processSecurity(ContractSecurity $security, $console = null): void
    {
        $contractNo = $security->contract?->contract_number ?? "ID#{$security->id}";
        $typeName   = $security->type?->name ?? 'Unknown Type';
        $label      = "[Security] #{$security->id} ({$typeName} — Contract: {$contractNo})";

        $daysViolated = $this->detectViolation($security, $console);

        if ($daysViolated === null) {
            if ($console) $console->line("  🟢 {$label} — Valid, no action needed.");
            return;
        }

        if ($console) $console->warn("  ⚠️ {$label} — Violation! Days in warning zone: {$daysViolated}");

        $this->triggerEscalation(
            $security,
            EscalationLog::CATEGORY_SECURITY,
            $daysViolated,
            null,   // no compliance_id for security
            $console
        );
    }

    /**
     * Detect violation: returns days_violated, or null if security is fine.
     */
    public function detectViolation(ContractSecurity $security, $console = null): ?int
    {
        if (!$security->issued_end_date) {
            if ($console) $console->line('  - Skip: No expiry date set.');
            return null;
        }

        $expiryDate   = Carbon::parse($security->issued_end_date)->startOfDay();
        $today        = now()->startOfDay();

        // Days remaining (negative = already expired)
        $daysRemaining = (int) $today->diffInDays($expiryDate, false);

        if ($console) {
            $status = $daysRemaining >= 0 ? "{$daysRemaining} days remaining" : abs($daysRemaining) . " days EXPIRED";
            $console->line("  - Expiry: {$expiryDate->toDateString()} ({$status})");
        }

        // Only flag if within the warning window or already expired
        if ($daysRemaining > self::WARNING_WINDOW_DAYS) {
            if ($console) $console->line('  - OK: Expiry is more than ' . self::WARNING_WINDOW_DAYS . ' days away.');
            return null;
        }

        // Calculate days_violated: how many days into the warning window we are
        // days_violated = WARNING_WINDOW - days_remaining
        // (when expired: days_remaining is negative, so days_violated > WARNING_WINDOW)
        $daysViolated = self::WARNING_WINDOW_DAYS - $daysRemaining;

        // Check matrix to confirm a rule applies at this level
        $rules = EscalationMatrix::getSecurityRules();
        if (EscalationMatrix::findApplicableDayMark($rules, $daysViolated) === null) {
            return null;
        }

        return $daysViolated;
    }
}