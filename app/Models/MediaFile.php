<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasS3Image;

class MediaFile extends Model
{
    use SoftDeletes, HasS3Image;

    // 1. Tell the trait which database column holds the S3 path
    protected string $imageColumn = 'path';

    protected $fillable = [
        'path',
        'type',
        'meta_data',
        'remark', // Added: Now mass-assignable via MediaFile::create()
        'lat',
        'long',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'lat' => 'float',  // Changed to float for better mathematical handling in PHP
        'long' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Expose the URL dynamically when the model is converted to an array or JSON
    protected $appends = ['url'];

    /**
     * Replaces the hardcoded local storage path with S3 resolution.
     */
    public function getUrlAttribute(): string
    {
        return $this->image_url; 
    }

    /**
     * ARCHITECTURE FIX: Native Eloquent relationships do not support foreign keys 
     * stored inside JSON arrays. Returning a Builder for manual querying.
     */
    public function workProgresses(): Builder
    {
        return WorkProgressData::whereJsonContains('images', $this->id);
    }

    /**
     * Presentation logic for LightGallery.
     * Updated to include the new remark field.
     */
    public function toLightGallery(): array
    {
        $name = $this->meta_data['name'] ?? 'Media';
        $subject = $this->meta_data['subject'] ?? 'No Subject';
        // Use the remark column, fallback to an empty string if null
        $remark = $this->remark ?? ''; 

        return [
            'id' => $this->id,
            'src' => $this->url,
            'thumb' => $this->url,
            'subHtml' => "<h4>{$name}</h4><p>Subject: {$subject}</p><p><i>{$remark}</i></p>"
        ];
    }
}