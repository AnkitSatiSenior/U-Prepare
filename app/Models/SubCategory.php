<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use SoftDeletes;

    protected $table = 'sub_category';

    protected $fillable = [
        'category_id',
        'name',
        'status',
    ];

    /**
     * Parent Category
     */
    public function category()
    {
        return $this->belongsTo(ProjectsCategory::class, 'category_id');
    }

    /**
     * Package Projects linked with this Sub-category
     */
    public function projects()
    {
        return $this->hasMany(PackageProject::class, 'package_sub_category_id');
    }

    /**
     * Safeguard Entries linked with this Sub-category
     */
    public function safeguardEntries()
    {
        return $this->hasMany(AlreadyDefineSafeguardEntry::class, 'category_id');
    }
}
