<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class Slide extends Model
{
    protected $fillable = [
        'img',
        'head',
        'subh',
        'btn_text',
        'link',
        'order',
        'status',
    ];

    /**
     * The accessors to append to the model's array/JSON form.
     * Crucial for API readiness.
     */
    protected $appends = ['image_url'];

    /**
     * Get the resolved S3 URL for the slider image.
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->img)) {
            return asset('images/default-slider.jpg');
        }

        try {
            return Storage::disk('s3')->url($this->img);
        } catch (Exception $e) {
            Log::error('Failed to resolve S3 URL for Slide', [
                'slide_id' => $this->id ?? 'unsaved',
                'path' => $this->img,
                'error' => $e->getMessage()
            ]);
            
            return asset('images/default-slider.jpg');
        }
    }
}