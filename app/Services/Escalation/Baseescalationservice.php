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
 * It contains the shared engine logic.
 */
abstract class BaseEscalationService
{
    // ─────────────────────────────────────────────────────────────────────────
    // CORE ENGINE — shared by all category services
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolve the escalation matrix rule and dispatch actions.
     */
    protected function triggerEscalation(
        Model $escalatable,
        string $category,
        int $daysViolated,
        ?SafeguardCompliance $compliance = null,
        $console = null
    ): void {
        $rules          = EscalationMatrix::getRulesFor($category);
        $applicableMark = EscalationMatrix::findApplicableDayMark($rules, $daysViolated);

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
    }

    /**
     * Execute one action from the matrix.
     */
    private function executeAction(
        Model $escalatable,
        string $category,
        int $dayMark,
        array $action,
        int $actualDaysViolated,
        ?SafeguardCompliance $compliance,
        $console = null
    ): void {
        $level        = $action['level'];
        $complianceId = $compliance?->id; // null for non-social types

        // ── 1. IDEMPOTENCY ────────────────────────────────────────────────────
        $alreadySent = DB::table('escalation_logs')
            ->where('escalatable_id',      $escalatable->id)
            ->where('escalatable_type',    get_class($escalatable))
            ->where('escalation_category', $category)
            ->where('compliance_id',       $complianceId)
            ->where('day_mark',            $dayMark)
            ->where('level',               $level)
            ->exists();

        if ($alreadySent) {
            if ($console) $console->line("  - 🔴 SKIPPED Level {$level}: Idempotency lock — already sent.");
            return; // FIXED: Prevent execution if already sent
        }

        // ── 2. USER LOOKUP ────────────────────────────────────────────────────
        $usersToNotify = $this->resolveUsersForCategory(
            $escalatable,
            $category,
            $level,
            $complianceId
        );

        if ($usersToNotify->isEmpty()) {
            if ($console) $console->line("  - 🔴 SKIPPED Level {$level}: No users assigned at this level for this project.");
            return; // FIXED: Prevent execution if no users found
        }

        // ── 3. BUILD NOTIFICATION PAYLOAD ────────────────────────────────────
        $isAlert      = $action['type'] === 'alert';
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
                );
            }
        } // FIXED: Closed foreach loop

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
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // USER RESOLUTION
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolve which users to notify.
     */
    private function resolveUsersForCategory(
        Model $escalatable,
        string $category,
        int $level,
        ?int $complianceId
    ) {
        return match ($category) {
            EscalationLog::CATEGORY_SOCIAL => User::mappedToEscalation(
                [$level],
                $escalatable->id, 
                $complianceId
            )->get(),
            
            EscalationLog::CATEGORY_PHYSICAL,
            EscalationLog::CATEGORY_FINANCIAL => $this->getUsersForSubProject(
                $escalatable->id, 
                $level
            ),
            
            EscalationLog::CATEGORY_SECURITY => $this->getUsersForSecurity(
                $escalatable, 
                $level
            ),
            
            default => collect(),
        };
    }

    /**
     * Get users at $level assigned to a sub-project.
     */
    private function getUsersForSubProject(int $subPackageProjectId, int $level)
    {
        // FIXED: Closed nested closures correctly
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
        })
        ->get();
    }

    /**
     * Get users assigned to any sub-project under the security's contract package.
     */
    private function getUsersForSecurity(Model $security, int $level)
    {
        $subProjectIds = optional($security->contract?->project)
            ->subProjects()
            ?->pluck('id')
            ?->toArray() ?? [];

        if (empty($subProjectIds)) {
            return collect();
        }

        // FIXED: Reused subProject logic safely with array evaluation
        return User::where(function ($q) use ($level) {
            $q->whereHas('role',        fn($r) => $r->where('level', $level))
              ->orWhereHas('designation', fn($d) => $d->where('level', $level));
        })
        ->whereExists(function ($q) use ($subProjectIds) {
            $q->select(DB::raw(1))
              ->from('user_safeguard_subpackage as uss')
              ->whereColumn('uss.user_id', 'users.id')
              ->whereIn('uss.sub_package_project_id', $subProjectIds)
              ->whereNull('uss.deleted_at');
        })
        ->get();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function categoryLabel(string $category): string
    {
        return EscalationLog::categoryLabels()[$category] ?? ucfirst($category);
    }

    private function resolveEntityName(Model $escalatable, string $category): string
    {
        if ($category === EscalationLog::CATEGORY_SECURITY) {
            $secType    = $escalatable->type?->name ?? 'Security';
            $contractNo = $escalatable->contract?->contract_number ?? "ID#{$escalatable->id}";
            return "{$secType} (Contract: {$contractNo})";
        }
        return $escalatable->name ?? "Entity ID#{$escalatable->id}";
    }

    private function violationDescription(string $category): string
    {
        return match ($category) {
            EscalationLog::CATEGORY_SOCIAL    => "Pre-Construction phase is incomplete while During Construction entries have already started. Please resolve immediately.",
            EscalationLog::CATEGORY_PHYSICAL  => "No physical progress (BOQ/EPC) has been recorded for this project within the expected timeframe. Please submit progress data immediately.",
            EscalationLog::CATEGORY_FINANCIAL => "No financial bill submission has been recorded for this project within the expected billing interval. Please submit billing data immediately.",
            EscalationLog::CATEGORY_SECURITY  => "A contract security certificate is near expiry or has already expired. Please renew the security document immediately to avoid contract compliance issues.",
            default                           => "A violation has been detected. Please take action immediately.",
        };
    }
}