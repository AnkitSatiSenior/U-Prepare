<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\HasS3Image;

class Leader extends Model
{
    use HasFactory, HasS3Image;

    // Explicitly define which column contains the S3 path
    protected string $imageColumn = 'img';

    protected $fillable = [
        'name',
        'title',
        'img',
        'status',
        'order',
    ];

    // Ensure the S3 URL is always present in JSON/API responses
    protected $appends = ['image_url'];

    protected static function booted(): void
    {
        // Clear cache so the frontend sees the new S3 links immediately
        static::saved(fn () => Cache::forget('active_leaders'));
        static::deleted(fn () => Cache::forget('active_leaders'));
    }
}