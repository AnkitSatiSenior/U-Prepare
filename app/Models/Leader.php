<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\HasS3Image;

class Leader extends Model
{
    use HasFactory, HasS3Image;

    // Tell the trait which database column holds the path
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
     * Laravel will automatically find getImageUrlAttribute() inside the HasS3Image trait!
     */
    protected $appends = ['image_url'];

    // 🚨 ARCHITECTURE FIX: 
    // getImageUrlAttribute() has been completely removed from here. 
    // The HasS3Image trait now safely injects it without being overwritten.

    /**
     * The "booted" method of the model.
     * Handles event-driven cache invalidation.
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