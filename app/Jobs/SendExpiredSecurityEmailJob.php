<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\ContractSecurity;
use App\Models\MailLog;
use App\Mail\SecurityExpiredMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendExpiredSecurityEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public function __construct(
        public ContractSecurity $security,
        public User $user,
        public bool $isReminder
    ) {}

    public function handle(): void
    {
        $mailable = new SecurityExpiredMail($this->security, $this->user, $this->isReminder);
        $subject = $mailable->envelope()->subject;
        
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
            Mail::to($this->user->email)->send($mailable);
            $logData['status'] = 'sent';
            MailLog::logMail($logData);
        } catch (Exception $e) {
            $logData['status'] = 'failed';
            $logData['error_message'] = $e->getMessage();
            MailLog::logMail($logData);
            
            throw $e; // Re-throw for Laravel queue to handle retries
        }
    }
}