<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\EscalationLog;
use App\Models\SubPackageProject;
use App\Models\ContractSecurity;
use App\Models\SafeguardCompliance;
use App\Services\Escalation\EscalationService;
use App\Services\Escalation\PhysicalProgressEscalationService;
use App\Services\Escalation\FinancialEscalationService;
use App\Services\Escalation\SecurityEscalationService;
use App\Services\Escalation\EscalationMatrix;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * ESCALATION ENGINE — TEST COMMAND
 *
 * Usage examples:
 *
 *   # Dry-run ALL categories for ALL projects (safe — no emails or WhatsApp sent)
 *   php artisan escalation:test --dry-run
 *
 *   # Test only Social Safeguard, dry-run
 *   php artisan escalation:test --category=social_safeguard --dry-run
 *
 *   # Test a SPECIFIC sub-project (by ID), all categories, dry-run
 *   php artisan escalation:test --project=5 --dry-run
 *
 *   # Test a specific ContractSecurity (by ID), dry-run
 *   php artisan escalation:test --security=3 --dry-run
 *
 *   # Live run (REAL emails + WhatsApp will be dispatched!)
 *   php artisan escalation:test --category=physical_progress
 *
 *   # Show the escalation matrix rules for a category
 *   php artisan escalation:test --show-matrix=financial_progress
 *
 *   # Wipe ALL escalation logs (reset idempotency locks — useful for re-testing)
 *   php artisan escalation:test --reset-logs
 *
 *   # Reset logs for one category only
 *   php artisan escalation:test --reset-logs --category=contract_security
 */
class TestEscalationEngine extends Command
{
    protected $signature = 'escalation:test
        {--dry-run          : Simulate the engine — detect violations but DO NOT send emails/WhatsApp or write logs}
        {--category=        : Limit test to one category: social_safeguard | physical_progress | financial_progress | contract_security}
        {--project=         : Limit test to a specific SubPackageProject ID}
        {--security=        : Limit security test to a specific ContractSecurity ID}
        {--show-matrix=     : Print the escalation matrix rules for a given category and exit}
        {--reset-logs       : DELETE escalation_logs rows (resets idempotency so alerts re-fire). Combine with --category to target one type.}
    ';

    protected $description = 'Test and debug the Escalation Engine across all 4 categories (social, physical, financial, security).';

    public function __construct(
        private readonly EscalationService                 $escalationService,
        private readonly PhysicalProgressEscalationService $physicalService,
        private readonly FinancialEscalationService        $financialService,
        private readonly SecurityEscalationService         $securityService,
    ) {
        parent::__construct();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HANDLE
    // ─────────────────────────────────────────────────────────────────────────

    public function handle(): int
    {
        $this->newLine();
        $this->components->info('╔══════════════════════════════════════════════════╗');
        $this->components->info('║        ESCALATION ENGINE — TEST RUNNER           ║');
        $this->components->info('╚══════════════════════════════════════════════════╝');
        $this->newLine();

        // ── Option: Show Matrix ───────────────────────────────────────────────
        if ($matrixCategory = $this->option('show-matrix')) {
            return $this->showMatrix($matrixCategory);
        }

        // ── Option: Reset Logs ────────────────────────────────────────────────
        if ($this->option('reset-logs')) {
            return $this->resetLogs();
        }

        // ── Guard: Confirm live run ───────────────────────────────────────────
        $isDryRun = (bool) $this->option('dry-run');
        if (!$isDryRun) {
            $this->components->warn('⚠️  DRY-RUN is OFF. Real emails and WhatsApp messages WILL be dispatched!');
            if (!$this->confirm('Are you sure you want to run a LIVE test?', false)) {
                $this->components->info('Aborted. Add --dry-run to run safely.');
                return self::SUCCESS;
            }
        } else {
            $this->components->info('🔵 DRY-RUN mode is ON — no emails or WhatsApp will be sent, no logs written.');
        }

        $this->newLine();

        // ── Run selected categories ───────────────────────────────────────────
        $category   = $this->option('category');
        $projectId  = $this->option('project');
        $securityId = $this->option('security');

        $categoriesToRun = $category
            ? [$category]
            : [
                EscalationLog::CATEGORY_SOCIAL,
                EscalationLog::CATEGORY_PHYSICAL,
                EscalationLog::CATEGORY_FINANCIAL,
                EscalationLog::CATEGORY_SECURITY,
              ];

        $totalViolations = 0;

        foreach ($categoriesToRun as $cat) {
            $violations = match ($cat) {
                EscalationLog::CATEGORY_SOCIAL    => $this->runSocialTest($projectId, $isDryRun),
                EscalationLog::CATEGORY_PHYSICAL  => $this->runPhysicalTest($projectId, $isDryRun),
                EscalationLog::CATEGORY_FINANCIAL => $this->runFinancialTest($projectId, $isDryRun),
                EscalationLog::CATEGORY_SECURITY  => $this->runSecurityTest($securityId, $isDryRun),
                default => $this->unknownCategory($cat),
            };
            $totalViolations += $violations;
            $this->newLine();
        }

        // ── Summary ───────────────────────────────────────────────────────────
        $this->components->info('══════════════════════════════════════════════════');
        $this->components->info("TEST COMPLETE — {$totalViolations} violation(s) detected across all categories.");
        if ($isDryRun) {
            $this->components->warn('(Dry-run: no notifications were sent, no logs were written)');
        }

        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CATEGORY RUNNERS
    // ─────────────────────────────────────────────────────────────────────────

    private function runSocialTest(?string $projectId, bool $isDryRun): int
    {
        $this->components->info('▶ [1] SOCIAL SAFEGUARD — Testing...');

        $projects    = $this->resolveProjects($projectId);
        $compliances = SafeguardCompliance::all();
        $violations  = 0;

        $rows = [];

        foreach ($projects as $subProject) {
            foreach ($compliances as $compliance) {
                $daysViolated = $this->escalationService->detectSocialViolation($subProject, $compliance->id);

                if ($daysViolated === null) {
                    $rows[] = [$subProject->id, $subProject->name, $compliance->name, '🟢 OK', '—'];
                    continue;
                }

                $violations++;
                $rules   = EscalationMatrix::getSocialRules();
                $dayMark = EscalationMatrix::findApplicableDayMark($rules, $daysViolated);
                $actions = $dayMark ? collect($rules[$dayMark])->map(fn($a) => "L{$a['level']} {$a['type']}")->join(', ') : 'None';

                $rows[] = [
                    $subProject->id,
                    $subProject->name,
                    $compliance->name,
                    "🔴 VIOLATION — Day {$daysViolated}",
                    $isDryRun ? "(dry-run) would send: {$actions}" : "SENT: {$actions}",
                ];

                if (!$isDryRun) {
                    $this->escalationService->processSubProject($subProject);
                }
            }
        }

        $this->table(['Project ID', 'Project Name', 'Compliance', 'Status', 'Actions'], $rows);
        return $violations;
    }

    private function runPhysicalTest(?string $projectId, bool $isDryRun): int
    {
        $this->components->info('▶ [2] PHYSICAL PROGRESS — Testing...');

        $projects   = $this->resolveProjects($projectId);
        $violations = 0;
        $rows       = [];

        foreach ($projects as $subProject) {
            $daysViolated = $this->physicalService->detectViolation($subProject);

            if ($daysViolated === null) {
                $rows[] = [$subProject->id, $subProject->name, '🟢 OK', '—'];
                continue;
            }

            $violations++;
            $rules   = EscalationMatrix::getPhysicalRules();
            $dayMark = EscalationMatrix::findApplicableDayMark($rules, $daysViolated);
            $actions = $dayMark ? collect($rules[$dayMark])->map(fn($a) => "L{$a['level']} {$a['type']}")->join(', ') : 'None';

            $rows[] = [
                $subProject->id,
                $subProject->name,
                "🔴 VIOLATION — {$daysViolated} days since last entry",
                $isDryRun ? "(dry-run) would send: {$actions}" : "SENT: {$actions}",
            ];

            if (!$isDryRun) {
                $this->physicalService->processSubProject($subProject);
            }
        }

        $this->table(['Project ID', 'Project Name', 'Status', 'Actions'], $rows);
        return $violations;
    }

    private function runFinancialTest(?string $projectId, bool $isDryRun): int
    {
        $this->components->info('▶ [3] FINANCIAL PROGRESS — Testing...');

        $projects   = $this->resolveProjects($projectId);
        $violations = 0;
        $rows       = [];

        foreach ($projects as $subProject) {
            $daysViolated = $this->financialService->detectViolation($subProject);

            if ($daysViolated === null) {
                $rows[] = [$subProject->id, $subProject->name, '🟢 OK', '—'];
                continue;
            }

            $violations++;
            $rules   = EscalationMatrix::getFinancialRules();
            $dayMark = EscalationMatrix::findApplicableDayMark($rules, $daysViolated);
            $actions = $dayMark ? collect($rules[$dayMark])->map(fn($a) => "L{$a['level']} {$a['type']}")->join(', ') : 'None';

            $rows[] = [
                $subProject->id,
                $subProject->name,
                "🔴 VIOLATION — {$daysViolated} days since last billing",
                $isDryRun ? "(dry-run) would send: {$actions}" : "SENT: {$actions}",
            ];

            if (!$isDryRun) {
                $this->financialService->processSubProject($subProject);
            }
        }

        $this->table(['Project ID', 'Project Name', 'Status', 'Actions'], $rows);
        return $violations;
    }

    private function runSecurityTest(?string $securityId, bool $isDryRun): int
    {
        $this->components->info('▶ [4] CONTRACT SECURITY — Testing...');

        $securities = $securityId
            ? ContractSecurity::with(['contract.project', 'type'])->where('id', $securityId)->get()
            : ContractSecurity::with(['contract.project', 'type'])->get();

        if ($securities->isEmpty()) {
            $this->components->warn('No contract securities found in the database.');
            return 0;
        }

        $violations = 0;
        $rows       = [];

        foreach ($securities as $security) {
            $contractNo = $security->contract?->contract_number ?? 'N/A';
            $typeName   = $security->type?->name ?? 'Unknown';
            $expiry     = $security->issued_end_date ?? '—';

            $daysViolated = $this->securityService->detectViolation($security);

            if ($daysViolated === null) {
                $daysLeft = $security->issued_end_date
                    ? (int) now()->diffInDays(Carbon::parse($security->issued_end_date), false)
                    : '?';
                $rows[] = [$security->id, $contractNo, $typeName, $expiry, "🟢 OK ({$daysLeft}d left)", '—'];
                continue;
            }

            $violations++;
            $rules   = EscalationMatrix::getSecurityRules();
            $dayMark = EscalationMatrix::findApplicableDayMark($rules, $daysViolated);
            $actions = $dayMark ? collect($rules[$dayMark])->map(fn($a) => "L{$a['level']} {$a['type']}")->join(', ') : 'None';

            $rows[] = [
                $security->id,
                $contractNo,
                $typeName,
                $expiry,
                "🔴 Day {$daysViolated} into warning zone",
                $isDryRun ? "(dry-run) would send: {$actions}" : "SENT: {$actions}",
            ];

            if (!$isDryRun) {
                $this->securityService->processSecurity($security);
            }
        }

        $this->table(['Sec ID', 'Contract No', 'Type', 'Expiry', 'Status', 'Actions'], $rows);
        return $violations;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW MATRIX
    // ─────────────────────────────────────────────────────────────────────────

    private function showMatrix(string $category): int
    {
        $rules = EscalationMatrix::getRulesFor($category);

        if (empty($rules)) {
            $this->components->error("Unknown category: [{$category}]");
            $this->line('Valid categories: social_safeguard, physical_progress, financial_progress, contract_security');
            return self::FAILURE;
        }

        $label = EscalationLog::categoryLabels()[$category] ?? $category;
        $this->components->info("Escalation Matrix — {$label}");
        $this->newLine();

        $rows = [];
        foreach ($rules as $dayMark => $actions) {
            foreach ($actions as $action) {
                $rows[] = [
                    "Day {$dayMark}",
                    "Level {$action['level']}",
                    ucfirst($action['type']),
                    "#{$action['count_so_far']} contact at this level",
                ];
            }
        }

        $this->table(['Threshold', 'Who is Notified', 'Type', 'Note'], $rows);
        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RESET LOGS
    // ─────────────────────────────────────────────────────────────────────────

    private function resetLogs(): int
    {
        $category = $this->option('category');

        $label = $category
            ? (EscalationLog::categoryLabels()[$category] ?? $category)
            : 'ALL categories';

        $this->components->warn("⚠️  This will DELETE escalation log rows for: {$label}");
        $this->components->warn('This resets idempotency locks — alerts will fire again on the next run.');

        if (!$this->confirm("Confirm delete escalation_logs for [{$label}]?", false)) {
            $this->components->info('Aborted.');
            return self::SUCCESS;
        }

        $query = DB::table('escalation_logs');

        if ($category) {
            $query->where('escalation_category', $category);
        }

        $deleted = $query->delete();

        $this->components->info("✅ Deleted {$deleted} escalation log row(s) for [{$label}].");
        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function resolveProjects(?string $projectId)
    {
        if ($projectId) {
            $project = SubPackageProject::find((int) $projectId);
            if (!$project) {
                $this->components->error("SubPackageProject ID [{$projectId}] not found.");
                return collect();
            }
            return collect([$project]);
        }

        return SubPackageProject::all();
    }

    private function unknownCategory(string $cat): int
    {
        $this->components->error("Unknown category: [{$cat}]");
        $this->line('Valid: social_safeguard, physical_progress, financial_progress, contract_security');
        return 0;
    }
}