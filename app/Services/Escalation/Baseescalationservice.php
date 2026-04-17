<?php

namespace App\Services\Escalation;

use App\Models\User;
use App\Models\EscalationLog;
use App\Models\SafeguardCompliance;
use App\Jobs\SendEscalationEmailJob;
use App\Jobs\SendEscalationWhatsAppJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * BASE ESCALATION SERVICE
 *
 * All category-specific escalation services extend this class.
 * It contains the shared engine logic:
 *   - triggerEscalation()  → resolves the matrix, finds the applicable day-mark
 *   - executeAction()      → idempotency check, user lookup, job dispatch, DB log
 * Category services (Social, Physical, Financial, Security) only implement:
 *   - detectViolation()   → returns ?int $daysViolated
 *   - processSubProject() → calls detectViolation() then triggerEscalation()
 */
abstract class BaseEscalationService
{
    // ─────────────────────────────────────────────────────────────────────────
    // CORE ENGINE — shared by all category services
    /**
     * Resolve the escalation matrix rule and dispatch actions.
     *
     * @param Model                  $escalatable    The model being evaluated (SubPackageProject, ContractSecurity…)
     * @param string                 $category       EscalationLog::CATEGORY_* constant
     * @param int                    $daysViolated   Days elapsed since the violation started
     * @param SafeguardCompliance|null $compliance   Only needed for CATEGORY_SOCIAL
     * @param mixed                  $console        Optional Artisan console output
     */
    protected function triggerEscalation(
        Model $escalatable,
        string $category,
        int $daysViolated,
        ?SafeguardCompliance $compliance = null,
        $console = null
    ): void {
        $rules           = EscalationMatrix::getRulesFor($category);
        $applicableMark  = EscalationMatrix::findApplicableDayMark($rules, $daysViolated);
        if ($applicableMark === null) {
            if ($console) $console->line("  - No matrix rule qualifies for Day {$daysViolated} in [{$category}].");
            return;
        }
        if ($console) $console->line("  -> [{$category}] Applying rule for Day {$applicableMark} (actual: {$daysViolated} days)");
        foreach ($rules[$applicableMark] as $action) {
            $this->executeAction(
                $escalatable,
                $category,
                $applicableMark,
                $action,
                $daysViolated,
                $compliance,
                $console
            );
    }
     * Execute one action from the matrix:
     *   1. Idempotency check  — skip if already sent
     *   2. User lookup        — find users assigned at this level
     *   3. Job dispatch       — email + WhatsApp
     *   4. Log to DB          — record so we never send twice
    private function executeAction(
        int $dayMark,
        array $action,
        int $actualDaysViolated,
        ?SafeguardCompliance $compliance,
        $level       = $action['level'];
        $complianceId = $compliance?->id; // null for non-social types
        // ── 1. IDEMPOTENCY ────────────────────────────────────────────────────
        $alreadySent = DB::table('escalation_logs')
            ->where('escalatable_id',   $escalatable->id)
            ->where('escalatable_type', get_class($escalatable))
            ->where('escalation_category', $category)
            ->where('compliance_id',    $complianceId)
            ->where('day_mark',         $dayMark)
            ->where('level',            $level)
            ->exists();
        if ($alreadySent) {
            if ($console) $console->line("  - 🔴 SKIPPED Level {$level}: Idempotency lock — already sent.");
        // ── 2. USER LOOKUP ────────────────────────────────────────────────────
        $usersToNotify = $this->resolveUsersForCategory(
            $escalatable,
            $category,
            $level,
            $complianceId
        );
        if ($usersToNotify->isEmpty()) {
            if ($console) $console->line("  - 🔴 SKIPPED Level {$level}: No users assigned at this level for this project.");
        // ── 3. BUILD NOTIFICATION PAYLOAD ────────────────────────────────────
        $isAlert     = $action['type'] === 'alert';
        $messageTitle = $isAlert
            ? "⚠️ URGENT ALERT: {$this->categoryLabel($category)} Violation"
            : "⚠️ REMINDER #{$action['count_so_far']}: {$this->categoryLabel($category)} Violation";
        $entityName = $this->resolveEntityName($escalatable, $category);
        $whatsappMessage = "{$messageTitle}\n\n"
            . "Project/Item: {$entityName}\n"
            . ($compliance ? "Compliance: {$compliance->name}\n" : '')
            . "Overdue By: {$actualDaysViolated} Days\n\n"
            . $this->violationDescription($category);
        // ── 4. DISPATCH JOBS ──────────────────────────────────────────────────
        foreach ($usersToNotify as $user) {
            if ($console) $console->info("  - 🟢 DISPATCHING: User #{$user->id} ({$user->name}) at Level {$level}");
            if ($user->email) {
                SendEscalationEmailJob::dispatch(
                    $escalatable,
                    $category,
                    $compliance,
                    $user,
                    $action,
                    $actualDaysViolated
                );
            }
            $validPhone = SendEscalationWhatsAppJob::formatPhone($user->phone_no);
            if ($validPhone) {
                SendEscalationWhatsAppJob::dispatch(
                    $validPhone,
                    $whatsappMessage
        // ── 5. LOG TO DB ──────────────────────────────────────────────────────
        try {
            DB::table('escalation_logs')->insert([
                'escalatable_id'      => $escalatable->id,
                'escalatable_type'    => get_class($escalatable),
                'escalation_category' => $category,
                'compliance_id'       => $complianceId,
                'day_mark'            => $dayMark,
                'level'               => $level,
                'type'                => $action['type'],
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        } catch (\Exception $e) {
            // Unique constraint violation = race condition duplicate. Safe to ignore.
            Log::warning("Escalation log insert skipped (duplicate): {$e->getMessage()}");
    // USER RESOLUTION — finds correct users per category
     * Resolve which users to notify.
     * - Social Safeguard → uses user_safeguard_subpackage pivot (compliance-specific)
     * - Physical / Financial → uses user_safeguard_subpackage for the sub-project (any compliance)
     * - Security → uses user_safeguard_subpackage for any sub-project under the contract's package
    private function resolveUsersForCategory(
        int $level,
        ?int $complianceId
    ) {
        return match ($category) {
            EscalationLog::CATEGORY_SOCIAL => User::mappedToEscalation(
                [$level],
                $escalatable->id,  // sub_package_project_id
                $complianceId
            )->get(),
            EscalationLog::CATEGORY_PHYSICAL,
            EscalationLog::CATEGORY_FINANCIAL => $this->getUsersForSubProject(
                $level
            ),
            EscalationLog::CATEGORY_SECURITY => $this->getUsersForSecurity(
                $escalatable,       // ContractSecurity model
            default => collect(),
        };
     * Get users at $level assigned to a sub-project (any compliance).
     * Used for Physical and Financial escalation.
    private function getUsersForSubProject(int $subPackageProjectId, int $level)
    {
        return User::where(function ($q) use ($level) {
            $q->whereHas('role',        fn($r) => $r->where('level', $level))
              ->orWhereHas('designation', fn($d) => $d->where('level', $level));
        })
        ->whereExists(function ($q) use ($subPackageProjectId) {
            $q->select(DB::raw(1))
              ->from('user_safeguard_subpackage as uss')
              ->whereColumn('uss.user_id', 'users.id')
              ->where('uss.sub_package_project_id', $subPackageProjectId)
              ->whereNull('uss.deleted_at');
        ->get();
     * Get users assigned to any sub-project under the security's contract package.
     * Used for Security escalation (ContractSecurity → Contract → PackageProject → SubPackageProjects).
    private function getUsersForSecurity(Model $security, int $level)
        // ContractSecurity → Contract → PackageProject → SubPackageProjects
        $subProjectIds = optional($security->contract?->project)
            ->subProjects()
            ?->pluck('id')
            ?->toArray() ?? [];
        if (empty($subProjectIds)) {
            return collect();
        ->whereExists(function ($q) use ($subProjectIds) {
              ->whereIn('uss.sub_package_project_id', $subProjectIds)
    // HELPERS
    private function categoryLabel(string $category): string
        return EscalationLog::categoryLabels()[$category] ?? ucfirst($category);
    private function resolveEntityName(Model $escalatable, string $category): string
        if ($category === EscalationLog::CATEGORY_SECURITY) {
            $secType    = $escalatable->type?->name ?? 'Security';
            $contractNo = $escalatable->contract?->contract_number ?? "ID#{$escalatable->id}";
            return "{$secType} (Contract: {$contractNo})";
        return $escalatable->name ?? "Entity ID#{$escalatable->id}";
    private function violationDescription(string $category): string
            EscalationLog::CATEGORY_SOCIAL    => "Pre-Construction phase is incomplete while During Construction entries have already started. Please resolve immediately.",
            EscalationLog::CATEGORY_PHYSICAL  => "No physical progress (BOQ/EPC) has been recorded for this project within the expected timeframe. Please submit progress data immediately.",
            EscalationLog::CATEGORY_FINANCIAL => "No financial bill submission has been recorded for this project within the expected billing interval. Please submit billing data immediately.",
            EscalationLog::CATEGORY_SECURITY  => "A contract security certificate is near expiry or has already expired. Please renew the security document immediately to avoid contract compliance issues.",
            default                           => "A violation has been detected. Please take action immediately.",
}