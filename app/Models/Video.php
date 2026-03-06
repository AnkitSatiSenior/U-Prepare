<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasS3Image;

class Video extends Model
{
    use HasFactory, SoftDeletes, HasS3Image;

    protected $fillable = [
        'img',
        'link',
        'text',
        'status',
        'order',
    ];

    /**
     * Appends the dynamic S3 URL to model arrays and JSON responses.
     */
    protected $appends = ['image_url'];
}