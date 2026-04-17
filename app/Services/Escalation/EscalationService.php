<?php

namespace App\Services\Escalation;

// --- ALL REQUIRED IMPORTS ---
use App\Models\User;
use App\Models\SubPackageProject;
use App\Models\SafeguardCompliance;
use App\Jobs\SendEscalationEmailJob;
use App\Jobs\SendEscalationWhatsAppJob;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EscalationService
{
    /**
     * Process a single SubPackageProject.
     */
    public function processSubProject(SubPackageProject $subProject, $console = null)
    {
        if ($console) $console->info("Evaluating SubProject: [ID: {$subProject->id}] {$subProject->name}");

        // Fetch all active compliance categories (e.g., 1 for Environmental, 2 for Social)
        $compliances = SafeguardCompliance::all();

        foreach ($compliances as $compliance) {
            $daysViolated = $this->detectViolation($subProject, $compliance->id, $console);

            if ($daysViolated !== null) {
                if ($console) $console->warn("  ⚠️ [{$compliance->name}] Violation Detected! Days Elapsed: {$daysViolated}");
                $this->triggerEscalation($subProject, $compliance, $daysViolated, $console);
            } else {
                if ($console) $console->line("  🟢 [{$compliance->name}] No Violation.");
            }
        }
    }

    /**
     * VIOLATION DETECTOR: Checks if Phase 2 started but Phase 1 is incomplete.
     */
    public function detectViolation(SubPackageProject $subProject, int $complianceId, $console = null): ?int
    {
        // 1. Fetch progress specifically for THIS compliance type
        $progressData = $subProject->socialSafeguardProgress($complianceId);
        
        if (empty($progressData[$complianceId])) {
            if ($console) $console->line("  - Skipping: No safeguard data entered yet.");
            return null; 
        }

        $phases = collect($progressData[$complianceId]['phases']);
        $preConstruction = $phases->firstWhere('id', 1);

        // CHECK 1: If Pre-Construction is 100% complete, no violation.
        if ($preConstruction && $preConstruction['percent'] >= 100) {
            if ($console) $console->line("  - Skipping: Pre-Construction is 100% complete.");
            return null; 
        }

        // CHECK 2: Has 'During Construction' (Phase 2) started?
        $firstDuringEntry = DB::table('social_safeguard_entries')
            ->where('sub_package_project_id', $subProject->id)
            ->where('social_compliance_id', $complianceId)
            ->where('contraction_phase_id', 2)
            ->orderBy('date_of_entry', 'asc')
            ->first();

        if (!$firstDuringEntry) {
            if ($console) $console->line("  - Skipping: During Construction (Phase 2) has not started yet.");
            return null; // Phase 2 hasn't started yet
        }

        // Calculate exact days violated
        $startDate = Carbon::parse($firstDuringEntry->date_of_entry);
        return (int) $startDate->startOfDay()->diffInDays(now()->startOfDay());
    }

    /**
     * USES THE ESCALATION MATRIX: Determines which threshold rule to apply.
     */
    private function triggerEscalation(SubPackageProject $subProject, SafeguardCompliance $compliance, int $daysViolated, $console = null)
    {
        // 🚨 THIS CALLS YOUR ESCALATION MATRIX FILE 🚨
        $rules = EscalationMatrix::getRules();
        $applicableDayMark = null;

        // Sort days descending (30, 28, 26... 1) to find the highest crossed threshold
        $sortedDays = array_keys($rules);
        rsort($sortedDays); 

        foreach ($sortedDays as $day) {
            if ($daysViolated >= $day) {
                $applicableDayMark = $day;
                break;
            }
        }

        if ($applicableDayMark === null) {
            if ($console) $console->line("  - No escalation rules qualify for Day {$daysViolated}.");
            return; 
        }

        if ($console) $console->line("  -> Applying Matrix Rule for Day {$applicableDayMark} (Actual Days: {$daysViolated})");

        // Execute the specific actions defined in the Matrix
        foreach ($rules[$applicableDayMark] as $action) {
            $this->executeAction($subProject, $compliance, $applicableDayMark, $action, $daysViolated, $console);
        }
    }

    /**
     * Executes the action: Checks idempotency, finds users, dispatches Jobs, and logs it.
     */
    private function executeAction(SubPackageProject $subProject, SafeguardCompliance $compliance, int $dayMark, array $action, int $actualDaysViolated, $console = null)
    {
        $level = $action['level'];

        // 1. Idempotency Check: Prevent duplicate alerts for the same threshold
        $alreadySent = DB::table('escalation_logs')
            ->where('escalatable_id', $subProject->id)
            ->where('escalatable_type', get_class($subProject))
            ->where('compliance_id', $compliance->id)
            ->where('day_mark', $dayMark)
            ->where('level', $level)
            ->exists();

        if ($alreadySent) {
            if ($console) $console->line("  - 🔴 SKIPPED Level {$level}: Alert already sent previously (Idempotency Lock).");
            return; 
        }

        // 2. Fetch Targeted Users strictly mapped to this project & compliance
        $usersToNotify = User::mappedToEscalation([$level], $subProject->id, $compliance->id)->get();

        if ($usersToNotify->isEmpty()) {
            if ($console) $console->line("  - 🔴 SKIPPED Level {$level}: No users at this level are assigned to this project's {$compliance->name} compliance.");
            return; 
        }

        // --- PREPARE THE WHATSAPP MESSAGE TEXT ONCE ---
        $isAlert = $action['type'] === 'alert';
        $messageTitle = $isAlert 
            ? "⚠️ URGENT ALERT: Compliance Violation" 
            : "⚠️ REMINDER #{$action['count_so_far']}: Compliance Violation";

        $whatsappMessage = "{$messageTitle}\n\n"
                         . "Project: {$subProject->name}\n"
                         . "Compliance: {$compliance->name}\n"
                         . "Overdue By: {$actualDaysViolated} Days\n\n"
                         . "Phase 1 is incomplete while Phase 2 has started. Please resolve immediately.";

        // 3. Dispatch Jobs for each user
        foreach ($usersToNotify as $user) {
            if ($console) $console->info("  - 🟢 DISPATCHING JOBS: To User ID {$user->id} ({$user->name}) at Level {$level}.");
            
            // Dispatch Email Job
            if ($user->email) {
                SendEscalationEmailJob::dispatch($subProject, $compliance, $user, $action, $actualDaysViolated);
            }

            // Dispatch WhatsApp Job
            if ($user->phone_no) {
                $validPhone = $user->phone_no; 
                SendEscalationWhatsAppJob::dispatch($subProject, $user, $validPhone, $whatsappMessage);
            }
        }

        // 4. Record to Database so we can view it in the UI and prevent duplicates
        DB::table('escalation_logs')->insert([
            'escalatable_id'   => $subProject->id,
            'escalatable_type' => get_class($subProject),
            'compliance_id'    => $compliance->id,
            'day_mark'         => $dayMark,
            'level'            => $level,
            'type'             => $action['type'],
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }
}