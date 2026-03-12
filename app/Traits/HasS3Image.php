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
    public function getFileUrl(string $column = 'contract_document', bool $temporary = false): string
    {
        $path = $this->{$column};
        $fallback = asset('images/default-document.png');

        if (empty($path)) {
            return $fallback;
        }

        try {
            if ($temporary) {
                // Generates a 60-minute signed URL for private S3 files
                return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60));
            }
            return Storage::disk('s3')->url($path);
        } catch (Exception $e) {
            Log::error('S3 URL Resolution Error', [
                'model' => static::class,
                'path'  => $path,
                'error' => $e->getMessage()
            ]);
            return $fallback;
        }
    }
}