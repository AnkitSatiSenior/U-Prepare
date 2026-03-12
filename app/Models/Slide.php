<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasS3Image;

class Slide extends Model
{
    use HasS3Image;

    // ✅ Tell the trait which database column holds the S3 object key
    protected string $imageColumn = 'img';

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

    // 🚨 ARCHITECTURE FIX: 
    // The getImageUrlAttribute() method has been completely deleted from this class.
    // By removing it, we prevent the PHP "method shadowing" bug. 
    // Laravel will automatically route the 'image_url' append request directly 
    // to the HasS3Image trait, which will safely resolve the S3 URL.
}