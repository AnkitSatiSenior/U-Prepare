<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\HasS3Image;

class PackageComponent extends Model
{
    use SoftDeletes, HasS3Image;

    /**
     * Instructs the HasS3Image trait to use the 'image' column 
     * instead of its default 'img' column.
     */
    protected string $imageColumn = 'image';

    protected $fillable = [
        'name',
        'budget',
        'description',
        'image',
        'page_hin_title',
        'page_eng_title',
        'hin_content',
        'eng_content',
    ];

    /**
     * Appends dynamic attributes to the model's array/JSON form.
     * This guarantees your API and frontend views always have access 
     * to the resolved S3 URL and localized text.
     */
    protected $appends = [
        'image_url', 
        'localized_title', 
        'localized_content'
    ];

    /**
     * Encapsulate Title localization logic in the Domain layer.
     */
    public function getLocalizedTitleAttribute(): string
    {
        $isHindi = request()->cookie('lang') === 'hi';
        return $isHindi ? (string) $this->page_hin_title : (string) $this->page_eng_title;
    }

    /**
     * Encapsulate Content localization logic in the Domain layer.
     */
    public function getLocalizedContentAttribute(): string
    {
        $isHindi = request()->cookie('lang') === 'hi';
        return $isHindi ? (string) $this->hin_content : (string) $this->eng_content;
    }

    /**
     * Get the package projects associated with this component.
     */
    public function packageProjects()
    {
        return $this->hasMany(PackageProject::class, 'package_component_id');
    }
}