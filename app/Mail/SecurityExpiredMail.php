<?php

namespace App\Mail;

use App\Models\ContractSecurity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecurityExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContractSecurity $security,
        public User $user,
        public bool $isReminder
    ) {}

    public function envelope(): Envelope
    {
        $prefix = $this->isReminder ? 'REMINDER:' : 'ACTION REQUIRED:';
        $subject = "{$prefix} Contract Security Expired [SEC-{$this->security->id}]";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.securities.expired',
            with: [
                'securityName'   => $this->security->type->name ?? 'Security',
                'contractNumber' => $this->security->contract->contract_number ?? 'N/A',
                // Defensively parse the string into a Carbon object before formatting
                'endDate'        => Carbon::parse($this->security->issued_end_date)->format('Y-m-d'),
                'isReminder'     => $this->isReminder,
                'userName'       => $this->user->name ?? 'User'
            ]
        );
    }
}