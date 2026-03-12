<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'user_id',
        'security_id',
        'to_number',
        'message_body',
        'status',
        'response',
        'error_message',
        'sent_at',
    ];

    /**
     * Cast attributes to native types.
     */
    protected function casts(): array
    {
        return [
            'response' => 'array',
            'sent_at'  => 'datetime',
        ];
    }

    /** ------------------------------
     * Relationships
     * ------------------------------
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}