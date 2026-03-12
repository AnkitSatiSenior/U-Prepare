<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\HasS3Image; // 👈 Import your trait

class Leader extends Model
{
    use HasFactory, HasS3Image; // 👈 Use the trait

    // Tell the trait which database column holds the path (it defaults to 'img', so this is optional but explicit)
    protected string $imageColumn = 'img';

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
     * The logic for this is now safely handled globally by the HasS3Image trait!
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image_url; 
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