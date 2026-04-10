<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\ContractSecurity;
use App\Models\WhatsAppLog; 
use App\Models\User;
use App\Jobs\SendExpiredSecurityWhatsAppJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class NotifyExpiredSecuritiesWhatsAppAction
{
    /**
     * Tracks the running total of seconds to delay the next job.
     */
    private int $cumulativeDelaySeconds = 0;

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

        // Extract valid users, checking the correct `phone_no` column
        $assignees = $project->assignments
            ->pluck('assignee')
            ->filter(function (?User $user) {
                return $user !== null && $this->formatPhoneNumber($user->phone_no) !== null;
            })
            ->unique('id');

        foreach ($assignees as $user) {
            $validPhone = $this->formatPhoneNumber($user->phone_no);
            $isReminder = $this->hasBeenNotified($security->id, $validPhone);
            
            // Build the message with the user's name and contract link
            $message = $this->buildMessage($security, $user, $isReminder);

            // Add a random delay between 5 and 10 seconds to the running total
            $this->cumulativeDelaySeconds += random_int(5, 10);

            // Dispatch the job with the calculated delay
            dispatch(new SendExpiredSecurityWhatsAppJob($security, $user, $validPhone, $message))
                ->delay(now()->addSeconds($this->cumulativeDelaySeconds));
            
            Log::info("Queued expired security WhatsApp message.", [
                'security_id'    => $security->id,
                'user_id'        => $user->id,
                'dispatch_phone' => $validPhone,
                'delay_seconds'  => $this->cumulativeDelaySeconds
            ]);
        }
    }

    /**
     * Sanitizes and formats the phone number for the WhatsApp API.
     */
    private function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) return null;

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($cleanPhone) === 10) {
            return '91' . $cleanPhone;
        }

        if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
            return $cleanPhone;
        }

        if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
            return '91' . substr($cleanPhone, 1);
        }

        return null;
    }

    private function hasBeenNotified(int $securityId, string $phone): bool
    {
        $tag = "[SEC-{$securityId}]";
        
        return WhatsAppLog::where('to_number', $phone)
            ->where('message_body', 'LIKE', "%{$tag}%")
            ->exists();
    }

    /**
     * Constructs the WhatsApp message with dynamic variables and URLs.
     */
    private function buildMessage(ContractSecurity $security, User $user, bool $isReminder): string
    {
        $prefix = $isReminder ? "⏳ *Reminder:*" : "🚨 *Alert:*";
        
        // Correctly target the 'package_name' property from PackageProject
        $projectName = $security->contract->project->package_name ?? 'Unknown Project';
        
        // Construct the direct contract link
        $contractId = $security->contract_id;
        $contractUrl = "https://www.u-prepare.com/admin/contracts/{$contractId}";
        
        return "{$prefix} Hello *{$user->name}*,\n\n"
             . "The security document *[SEC-{$security->id}]* for project *{$projectName}* has expired.\n\n"
             . "🔗 *View Contract:* {$contractUrl}\n\n"
             . "Please log in to the portal to take necessary action immediately.";
    }
}