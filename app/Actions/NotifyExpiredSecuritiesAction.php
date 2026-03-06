<?php

namespace App\Actions;

use App\Models\ContractSecurity;
use App\Models\MailLog;
use App\Jobs\SendExpiredSecurityEmailJob;
use Illuminate\Database\Eloquent\Collection;

class NotifyExpiredSecuritiesAction
{
    public function execute(): void
    {
        // Chunk to avoid memory overload. Eager load relationships to prevent N+1.
        ContractSecurity::query()
            ->whereDate('issued_end_date', '<', now())
            ->with(['contract.project.assignments.assignee'])
            ->chunkById(100, function (Collection $securities) {
                foreach ($securities as $security) {
                    $this->processSecurity($security);
                }
            });
    }

    private function processSecurity(ContractSecurity $security): void
    {
        $project = $security->contract?->project;
        
        if (!$project || $project->assignments->isEmpty()) {
            return; // Guard: No project or no assigned users
        }

        $assignees = $project->assignments->pluck('assignee')->filter();

        foreach ($assignees as $user) {
            if (empty($user->email)) {
                continue; // Guard: User has no email
            }

            $isReminder = $this->hasBeenNotified($security->id, $user->email);

            // Dispatch to queue to prevent blocking the cron process
            dispatch(new SendExpiredSecurityEmailJob($security, $user, $isReminder));
        }
    }

    private function hasBeenNotified(int $securityId, string $email): bool
    {
        // Warning: String matching is not optimal for scaling. 
        // Suggestion for future: Add `entity_type` and `entity_id` to `mail_logs`.
        $tag = "[SEC-{$securityId}]";
        
        return MailLog::where('to_email', $email)
            ->where('subject', 'LIKE', "%{$tag}%")
            ->exists();
    }
}