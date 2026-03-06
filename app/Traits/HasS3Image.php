<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

trait HasS3Image
{
    /**
     * Get the resolved S3 URL.
     * Looks for a protected $imageColumn property on the model, defaults to 'img'.
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        $column = $this->imageColumn ?? 'img'; // Dynamic column resolution
        $fallback = asset('images/default-image.png');

        if (empty($this->{$column})) {
            return $fallback;
        }

        try {
            return Storage::disk('s3')->url($this->{$column});
        } catch (Exception $e) {
            Log::error('Failed to resolve S3 URL via Trait', [
                'model' => static::class,
                'path'  => $this->{$column},
                'error' => $e->getMessage()
            ]);
            
            return $fallback;
        }
    }
}