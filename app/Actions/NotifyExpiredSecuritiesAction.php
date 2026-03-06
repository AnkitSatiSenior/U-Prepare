<?php

namespace App\Actions;

use App\Models\ContractSecurity;
use App\Models\MailLog;
use App\Jobs\SendExpiredSecurityEmailJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class NotifyExpiredSecuritiesAction
{
    public function execute(): void
    {
        // 1. Identify Expired Securities
        // 2. Ensure they trace back to a project with assigned users
        // 3. Eager load the complete relationship graph
        ContractSecurity::query()
            ->whereDate('issued_end_date', '<', now())
            ->whereHas('contract.project.assignments') 
            ->with(['contract.project.assignments.assignee'])
            ->chunkById(100, function (Collection $securities) {
                foreach ($securities as $security) {
                    $this->processSecurity($security);
                }
            });
    }

    private function processSecurity(ContractSecurity $security): void
    {
        // Traverse the relationship graph safely
        $contract = $security->contract;
        if (!$contract) return;

        $project = $contract->project;
        if (!$project || $project->assignments->isEmpty()) return;

        // Extract valid users from the assignments
        $assignees = $project->assignments
            ->pluck('assignee')
            ->filter(fn($user) => $user !== null && !empty($user->email))
            ->unique('id'); // Prevent duplicate emails if assigned multiple times

        foreach ($assignees as $user) {
            $isReminder = $this->hasBeenNotified($security->id, $user->email);

            // Dispatch to the queue infrastructure
            dispatch(new SendExpiredSecurityEmailJob($security, $user, $isReminder));
            
            Log::info("Queued expired security email.", [
                'security_id' => $security->id,
                'user_id' => $user->id,
                'is_reminder' => $isReminder
            ]);
        }
    }

    private function hasBeenNotified(int $securityId, string $email): bool
    {
        $tag = "[SEC-{$securityId}]";
        
        return MailLog::where('to_email', $email)
            ->where('subject', 'LIKE', "%{$tag}%")
            ->exists();
    }
}