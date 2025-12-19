<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    // Fillable fields
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'is_read', // Boolean to track if recipient has read it
    ];

    // Casts
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Sender relationship
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Conversation relationship
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
