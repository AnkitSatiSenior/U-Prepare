<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'action',
        'changes',
        'ip_address',
        'user_agent',
        'url',
         'latitude',
    'longitude',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    /**
     * Relationship: user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: the model that was affected (polymorphic)
     */
    public function model()
    {
        return $this->morphTo(null, 'model_type', 'model_id');
    }

    /**
     * Record a manual log entry (for custom actions like "approved", "file_uploaded", etc.)
     */
    public static function record(string $action, $model = null, array $changes = null): void
    {
        self::create([
            'user_id' => auth()->id(),
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'action' => $action,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'url' => request()->fullUrl(),
        ]);
    }
}
