<?php

namespace App\Actions;

use App\Models\ContractSecurity;
use App\Models\WhatsAppLog; // Assuming you create a log model similar to MailLog
use App\Jobs\SendExpiredSecurityWhatsAppJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class NotifyExpiredSecuritiesWhatsAppAction
{
    public function execute(): void
    {
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
        $contract = $security->contract;
        if (!$contract) return;

        $project = $contract->project;
        if (!$project || $project->assignments->isEmpty()) return;

        // Extract valid users with PHONE NUMBERS
        $assignees = $project->assignments
            ->pluck('assignee')
            ->filter(fn($user) => $user !== null && !empty($user->phone)) // Ensure phone exists
            ->unique('id');

        foreach ($assignees as $user) {
            $isReminder = $this->hasBeenNotified($security->id, $user->phone);

            // Construct the message text (You can abstract this to a Blade view or Class later)
            $message = $this->buildMessage($security, $user, $isReminder);

            dispatch(new SendExpiredSecurityWhatsAppJob($security, $user, $message));
            
            Log::info("Queued expired security WhatsApp message.", [
                'security_id' => $security->id,
                'user_id'     => $user->id,
                'is_reminder' => $isReminder
            ]);
        }
    }

    private function hasBeenNotified(int $securityId, string $phone): bool
    {
        $tag = "[SEC-{$securityId}]";
        
        return WhatsAppLog::where('to_number', $phone)
            ->where('message_body', 'LIKE', "%{$tag}%")
            ->exists();
    }

    private function buildMessage(ContractSecurity $security, $user, bool $isReminder): string
    {
        $greeting = $isReminder ? "Reminder:" : "Alert:";
        return "{$greeting} Hello {$user->name}, the security document [SEC-{$securityId}] for project {$security->contract->project->name} has expired. Please take necessary action.";
    }
}