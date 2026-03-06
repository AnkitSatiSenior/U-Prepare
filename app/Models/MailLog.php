<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    use HasFactory;

    protected $table = 'mail_logs';

    // Must EXACTLY match the database columns from your SQL schema
    protected $fillable = [
        'subject', 
        'body', 
        'from_email', 
        'from_name', 
        'to_email', 
        'to_name', 
        'cc', 
        'bc', 
        'attachments', 
        'status', 
        'error_mesage', 
        'sent_at'
    ];

    /**
     * Casts based strictly on your DB columns.
     */
    protected $casts = [
        'sent_at'     => 'datetime',
        'cc'          => 'array',
        'bc'          => 'array',
        'attachments' => 'array',
    ];

   
    public static function logMail(array $data)
    {
        return self::create([
            'subject'      => $data['subject'] ?? null,
            'body'         => $data['body'] ?? null,
            'from_email'   => $data['from_email'] ?? null,
            'from_name'    => $data['from_name'] ?? null,
            'to_email'     => $data['to_email'] ?? null,
            'to_name'      => $data['to_name'] ?? null,
            'cc'           => $data['cc'] ?? [],
            // Accepts 'bcc' from standard code, falls back to 'bc' if passed directly
            'bc'           => $data['bcc'] ?? ($data['bc'] ?? []),
            'attachments'  => $data['attachments'] ?? [],
            'status'       => $data['status'] ?? 'queued',
            // Accepts 'error_message' from standard code, falls back to DB name
            'error_mesage' => $data['error_message'] ?? ($data['error_mesage'] ?? null),
            'sent_at'      => $data['sent_at'] ?? now(),
        ]);
    }
}