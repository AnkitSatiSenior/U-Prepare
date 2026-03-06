<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class Leader extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'img',
        'status',
        'order',
    ];

    /**
     * Appends the dynamic S3 URL to model arrays and JSON responses.
     */
    protected $appends = ['image_url'];

    /**
     * Get the resolved S3 URL for the leader's image.
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->img)) {
            // Provide a generic silhouette/avatar fallback
            return asset('images/default-leader.jpg'); 
        }

        try {
            return Storage::disk('s3')->url($this->img);
        } catch (Exception $e) {
            Log::error('Failed to resolve S3 URL for Leader', [
                'leader_id' => $this->id ?? 'unsaved',
                'path' => $this->img,
                'error' => $e->getMessage()
            ]);
            
            return asset('images/default-leader.jpg');
        }
    }

    /**
     * The "booted" method of the model.
     * Handles event-driven cache invalidation to replace time-based caching.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('active_leaders');
        });

        static::deleted(function () {
            Cache::forget('active_leaders');
        });
    }
}