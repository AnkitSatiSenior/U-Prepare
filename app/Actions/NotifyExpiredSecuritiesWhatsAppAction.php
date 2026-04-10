<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\ContractSecurity;
use App\Models\WhatsAppLog; 
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
        // 1. Safe traversal
        $contract = $security->contract;
        if (!$contract) return;

        $project = $contract->project;
        if (!$project || $project->assignments->isEmpty()) return;

        // 2. Extract and Sanitize Assignees
        $assignees = $project->assignments
            ->pluck('assignee')
            ->filter(function ($user) {
                // Remove user if null or if the phone number cannot be formatted correctly
                return $user !== null && $this->formatPhoneNumber($user->phone) !== null;
            })
            ->unique('id');

        // 3. Dispatch Notifications
        foreach ($assignees as $user) {
            $validPhone = $this->formatPhoneNumber($user->phone);
            $isReminder = $this->hasBeenNotified($security->id, $validPhone);
            $message    = $this->buildMessage($security, $user, $isReminder);

            // Note: Update your Job's constructor to accept the validated phone number
            dispatch(new SendExpiredSecurityWhatsAppJob($security, $user, $validPhone, $message));
            
            Log::info("Queued expired security WhatsApp message.", [
                'security_id'    => $security->id,
                'user_id'        => $user->id,
                'dispatch_phone' => $validPhone,
                'is_reminder'    => $isReminder
            ]);
        }
    }

    /**
     * Sanitizes and formats the phone number for the WhatsApp API.
     */
    private function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Strip everything except numbers (removes spaces, dashes, + signs)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Case 1: Exactly 10 digits (Standard Indian mobile) -> Prepend 91
        if (strlen($cleanPhone) === 10) {
            return '91' . $cleanPhone;
        }

        // Case 2: 12 digits and starts with 91 -> Already valid
        if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
            return $cleanPhone;
        }

        // Case 3: 11 digits and starts with 0 (e.g., 08868945220) -> Strip 0, prepend 91
        if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
            return '91' . substr($cleanPhone, 1);
        }

        // Return null if it doesn't match expected formats (prevents API failures)
        return null;
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
        $prefix = $isReminder ? "⏳ *Reminder:*" : "🚨 *Alert:*";
        $projectName = $security->contract->project->name ?? 'Unknown Project';
        
        // WhatsApp Markdown formatting applied
        return "{$prefix} Hello *{$user->name}*,\n\n"
             . "The security document *[SEC-{$security->id}]* for project *{$projectName}* has expired.\n\n"
             . "Please log in to the U-Prepare portal to take necessary action immediately.";
    }
}