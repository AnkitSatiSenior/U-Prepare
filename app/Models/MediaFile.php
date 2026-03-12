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
        'lat',
        'long',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'lat' => 'decimal:8',  // Standardized coordinate precision
        'long' => 'decimal:8',
    ];

    // Expose the URL dynamically when the model is converted to an array or JSON
    protected $appends = ['url'];

    /**
     * Replaces the hardcoded local storage path with S3 resolution.
     * Maps to the logic provided by your HasS3Image trait.
     */
    public function getUrlAttribute(): string
    {
        return $this->image_url; // Calls getImageUrlAttribute() from the HasS3Image trait
    }

    /**
     * ARCHITECTURE FIX: Native Eloquent relationships do not support foreign keys 
     * stored inside JSON arrays on the inverse side. We must return a Builder instance.
     * * @return Builder
     */
    public function workProgresses(): Builder
    {
        return WorkProgressData::whereJsonContains('images', $this->id);
    }

    /**
     * @deprecated Presentation logic should live in an API Resource or View Presenter.
     * Kept for backwards compatibility per request.
     */
    public function toLightGallery(): array
    {
        $name = $this->meta_data['name'] ?? 'Media';
        $subject = $this->meta_data['subject'] ?? 'No Subject';

        return [
            'id' => $this->id,
            'src' => $this->url, // Leverages the S3 accessor safely
            'thumb' => $this->url,
            'subHtml' => "<h4>{$name}</h4><p>Subject: {$subject}</p>"
        ];
    }
}