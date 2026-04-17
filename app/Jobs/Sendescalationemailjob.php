<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Models\MailLog;
use App\Models\EscalationLog;
use App\Models\SafeguardCompliance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Exception;

/**
 * SEND ESCALATION EMAIL JOB
 *
 * Generic email job for all 4 escalation categories.
 * Logs to mail_logs table (same as existing email jobs).
 */
class SendEscalationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly Model                $escalatable,
        public readonly string               $category,
        public readonly ?SafeguardCompliance $compliance,
        public readonly User                 $user,
        public readonly array                $action,
        public readonly int                  $daysViolated,
    ) {}

    public function handle(): void
    {
        $subject  = $this->buildSubject();
        $viewData = $this->buildViewData();

        $logData = [
            'subject'    => $subject,
            'to_email'   => $this->user->email,
            'to_name'    => $this->user->name ?? 'User',
            'from_email' => config('mail.from.address'),
            'from_name'  => config('mail.from.name'),
            'status'     => 'queued',
            'sent_at'    => now(),
        ];

        try {
            Mail::send('emails.escalation_alert', $viewData, function ($message) use ($subject) {
                $message->to($this->user->email, $this->user->name)
                        ->subject($subject);
            });

            $logData['status'] = 'sent';
            MailLog::create($logData);

        } catch (Exception $e) {
            $logData['status']        = 'failed';
            $logData['error_mesage']  = $e->getMessage(); // note: column typo in DB schema preserved
            MailLog::create($logData);

            throw $e; // Re-throw for queue retries
        }
    }

    private function buildSubject(): string
    {
        $label   = EscalationLog::categoryLabels()[$this->category] ?? $this->category;
        $isAlert = $this->action['type'] === 'alert';
        $prefix  = $isAlert ? '🚨 URGENT' : '⏰ Reminder #' . $this->action['count_so_far'];
        $entity  = $this->resolveEntityName();

        return "{$prefix}: {$label} Violation — {$entity} (Level {$this->action['level']})";
    }

    private function buildViewData(): array
    {
        return [
            'user'          => $this->user,
            'action'        => $this->action,
            'daysViolated'  => $this->daysViolated,
            'category'      => $this->category,
            'categoryLabel' => EscalationLog::categoryLabels()[$this->category] ?? $this->category,
            'isAlert'       => $this->action['type'] === 'alert',
            'entityName'    => $this->resolveEntityName(),
            'compliance'    => $this->compliance,
            'project'       => $this->escalatable, // backward compat
        ];
    }

    private function resolveEntityName(): string
    {
        if ($this->category === EscalationLog::CATEGORY_SECURITY) {
            $type       = $this->escalatable->type?->name ?? 'Security';
            $contractNo = $this->escalatable->contract?->contract_number ?? "ID#{$this->escalatable->id}";
            return "{$type} (Contract: {$contractNo})";
        }

        return $this->escalatable->name ?? "Entity ID#{$this->escalatable->id}";
    }
}