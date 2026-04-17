<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\EscalationLog;
use App\Models\SafeguardCompliance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * SEND ESCALATION EMAIL JOB — MULTI-CATEGORY
 *
 * A single, generic job that handles email dispatch for ALL 4 escalation types:
 *   - social_safeguard
 *   - physical_progress
 *   - financial_progress
 *   - contract_security
 *
 * Usage:
 *   SendEscalationEmailJob::dispatch($escalatable, $category, $compliance, $user, $action, $daysViolated);
 */
class SendEscalationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        private readonly Model                  $escalatable,
        private readonly string                 $category,
        private readonly ?SafeguardCompliance   $compliance,
        private readonly User                   $user,
        private readonly array                  $action,
        private readonly int                    $daysViolated,
    ) {}

    public function handle(): void
    {
        try {
            Mail::send(
                'emails.escalation_alert',
                $this->buildViewData(),
                function ($message) {
                    $subject = $this->resolveSubject();
                    $message->to($this->user->email, $this->user->name)
                            ->subject($subject);
                }
            );

            Log::info(
                "Escalation email sent",
                [
                    'category'        => $this->category,
                    'user_id'         => $this->user->id,
                    'escalatable_id'  => $this->escalatable->id,
                    'escalatable_type'=> get_class($this->escalatable),
                    'day_mark'        => $this->action['level'],
                    'days_violated'   => $this->daysViolated,
                ]
            );
        } catch (\Exception $e) {
            Log::error("Escalation email FAILED for User #{$this->user->id}: {$e->getMessage()}");
            throw $e; // Re-throw so the queue marks this job as failed and retries
        }
    }

    private function buildViewData(): array
    {
        $isAlert    = $this->action['type'] === 'alert';
        $entityName = $this->resolveEntityName();

        return [
            // Generic fields used by the blade template
            'user'          => $this->user,
            'action'        => $this->action,
            'daysViolated'  => $this->daysViolated,
            'category'      => $this->category,
            'categoryLabel' => EscalationLog::categoryLabels()[$this->category] ?? $this->category,
            'isAlert'       => $isAlert,
            'entityName'    => $entityName,
            'compliance'    => $this->compliance,

            // For backward compat with old blade template variable names
            'project'       => $this->escalatable,   // old social template used $project
        ];
    }

    private function resolveSubject(): string
    {
        $label    = EscalationLog::categoryLabels()[$this->category] ?? $this->category;
        $isAlert  = $this->action['type'] === 'alert';
        $prefix   = $isAlert ? '🚨 URGENT' : '⏰ Reminder';
        $entity   = $this->resolveEntityName();

        return "{$prefix}: {$label} Violation — {$entity} (Level {$this->action['level']})";
    }

    private function resolveEntityName(): string
    {
        if ($this->category === EscalationLog::CATEGORY_SECURITY) {
            $typeName   = $this->escalatable->type?->name ?? 'Security';
            $contractNo = $this->escalatable->contract?->contract_number ?? "ID#{$this->escalatable->id}";
            return "{$typeName} (Contract: {$contractNo})";
        }

        return $this->escalatable->name ?? "Entity ID#{$this->escalatable->id}";
    }
}