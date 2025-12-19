<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'title_hi',
        'body_eng',
        'body_hindi',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * Generate a unique slug for the page.
     */
    public static function createSlug($title)
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;
    
        while (self::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count++;
        }
    
        return $slug;
    }
}