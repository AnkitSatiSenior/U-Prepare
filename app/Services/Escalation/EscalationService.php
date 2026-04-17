<?php

namespace App\Services\Escalation;

use App\Models\SubPackageProject;
use App\Models\SafeguardCompliance;
use App\Models\EscalationLog;
use App\Jobs\SendEscalationEmailJob;
use App\Jobs\SendEscalationWhatsAppJob;
use App\Services\Escalation\BaseEscalationService;
use App\Services\Escalation\PhysicalProgressEscalationService;
use App\Services\Escalation\FinancialEscalationService;
use App\Services\Escalation\SecurityEscalationService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * MASTER ESCALATION SERVICE — ORCHESTRATOR
 *
 * This is the single entry point called by:
 *   - The Artisan scheduled command (runs daily via cron)
 *   - The manual "Run Engine" button in the admin UI
 *
 * It coordinates all 4 escalation categories:
 *   1. Social Safeguard   — Pre-Construction pending while During Construction started
 *   2. Physical Progress  — No BOQ/EPC entries in expected timeframe
 *   3. Financial Progress — No financial bill submission in expected interval
 *   4. Contract Security  — Security certificate near expiry or expired
 */
class EscalationService extends BaseEscalationService
{
    public function __construct(
        private readonly PhysicalProgressEscalationService $physicalService,
        private readonly FinancialEscalationService         $financialService,
        private readonly SecurityEscalationService          $securityService,
    ) {}

    /**
     * MAIN ENTRY POINT
     * Run the full escalation engine across all categories.
     */
    public function runFullEngine($console = null): void
    {
        if ($console) $console->info('════════════════════════════════════════════');
        if ($console) $console->info('  ESCALATION ENGINE — FULL RUN');
        if ($console) $console->info('════════════════════════════════════════════');

        $subProjects = SubPackageProject::all();

        if ($console) $console->info("Found {$subProjects->count()} sub-projects to evaluate.");
        if ($console) $console->newLine();

        // ── Category 1: Social Safeguard ──────────────────────────────────────
        if ($console) $console->info('▶ [1/4] Processing SOCIAL SAFEGUARD escalations...');
        foreach ($subProjects as $subProject) {
            $this->processSubProject($subProject, $console);
        }

        // ── Category 2: Physical Progress ─────────────────────────────────────
        if ($console) $console->newLine();
        if ($console) $console->info('▶ [2/4] Processing PHYSICAL PROGRESS escalations...');
        foreach ($subProjects as $subProject) {
            $this->physicalService->processSubProject($subProject, $console);
        }

        // ── Category 3: Financial Progress ────────────────────────────────────
        if ($console) $console->newLine();
        if ($console) $console->info('▶ [3/4] Processing FINANCIAL PROGRESS escalations...');
        foreach ($subProjects as $subProject) {
            $this->financialService->processSubProject($subProject, $console);
        }

        // ── Category 4: Contract Security ─────────────────────────────────────
        if ($console) $console->newLine();
        if ($console) $console->info('▶ [4/4] Processing CONTRACT SECURITY escalations...');
        $this->securityService->processAllSecurities($console);

        if ($console) $console->newLine();
        if ($console) $console->info('════════════════════════════════════════════');
        if ($console) $console->info('  ENGINE COMPLETE ✅');
        if ($console) $console->info('════════════════════════════════════════════');
    }

    /**
     * Process a single SubPackageProject for SOCIAL SAFEGUARD violations.
     * Kept for backward compatibility with existing command and controller code.
     */
    public function processSubProject(SubPackageProject $subProject, $console = null): void
    {
        if ($console) $console->line("Evaluating SubProject: [ID: {$subProject->id}] {$subProject->name}");

        $compliances = SafeguardCompliance::all();

        foreach ($compliances as $compliance) {
            $daysViolated = $this->detectSocialViolation($subProject, $compliance->id, $console);

            if ($daysViolated !== null) {
                if ($console) $console->warn("  ⚠️ [{$compliance->name}] Violation Detected! Days Elapsed: {$daysViolated}");
                $this->triggerEscalation($subProject, EscalationLog::CATEGORY_SOCIAL, $daysViolated, $compliance, $console);
            } else {
                if ($console) $console->line("  🟢 [{$compliance->name}] No Violation.");
            }
        }
    }

    /**
     * SOCIAL SAFEGUARD VIOLATION DETECTOR
     * Checks if Phase 2 (During Construction) started while Phase 1 (Pre-Construction) is incomplete.
     *
     * (Previously named detectViolation — renamed to avoid collision with BaseEscalationService contract.)
     */
    public function detectSocialViolation(SubPackageProject $subProject, int $complianceId, $console = null): ?int
    {
        $progressData = $subProject->socialSafeguardProgress($complianceId);

        if (empty($progressData[$complianceId])) {
            if ($console) $console->line('  - Skipping: No safeguard data entered yet.');
            return null;
        }

        $phases          = collect($progressData[$complianceId]['phases']);
        $preConstruction = $phases->firstWhere('id', 1);

        if ($preConstruction && $preConstruction['percent'] >= 100) {
            if ($console) $console->line('  - Skipping: Pre-Construction is 100% complete.');
            return null;
        }

        $firstDuringEntry = DB::table('social_safeguard_entries')
            ->where('sub_package_project_id', $subProject->id)
            ->where('social_compliance_id',   $complianceId)
            ->where('contraction_phase_id',   2)
            ->orderBy('date_of_entry', 'asc')
            ->first();

        if (!$firstDuringEntry) {
            if ($console) $console->line('  - Skipping: During Construction (Phase 2) has not started yet.');
            return null;
        }

        $startDate = Carbon::parse($firstDuringEntry->date_of_entry);
        return (int) $startDate->startOfDay()->diffInDays(now()->startOfDay());
    }

    /**
     * Backward-compat alias used by EscalationLogController::triggerEngine()
     * when it calls $service->processSubProject($project) in a loop.
     * This override ensures the controller still works after refactor.
     *
     * For a FULL engine run (all 4 categories), use runFullEngine() instead.
     */
}