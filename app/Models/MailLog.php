<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    use HasFactory;

    protected $table = 'mail_logs';

    protected $fillable = ['subject', 'body', 'from_email', 'from_name', 'to_email', 'to_name', 'cc', 'bcc', 'attachments', 'status', 'error_message', 'sent_at'];

    /**
     * Casts for automatic type conversion.
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'cc' => 'array',
        'bcc' => 'array',
        'attachments' => 'array',
    ];

    /**
     * Helper function for logging mail details easily.
     */
    public static function logMail(array $data)
    {
        return self::create([
            'subject' => $data['subject'] ?? null,
            'body' => $data['body'] ?? null,
            'from_email' => $data['from_email'] ?? null,
            'from_name' => $data['from_name'] ?? null,
            'to_email' => $data['to_email'] ?? null,
            'to_name' => $data['to_name'] ?? null,
            'cc' => $data['cc'] ?? [],
            'bcc' => $data['bcc'] ?? [],
            'attachments' => $data['attachments'] ?? [],
            'status' => $data['status'] ?? 'queued',
            'error_message' => $data['error_message'] ?? null,
            'sent_at' => $data['sent_at'] ?? now(),
        ]);
    }
}
