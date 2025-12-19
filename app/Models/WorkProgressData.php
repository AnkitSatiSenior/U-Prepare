<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkProgressData extends Model
{
    use HasFactory, SoftDeletes;

    // Table name
    protected $table = 'work_progress_data';

    // Mass assignable fields
    protected $fillable = [
        'project_id',
        'work_component_id',
        'qty_length',
        'current_stage',
        'progress_percentage',
        'remarks',
        'date_of_entry',
        'user_id',
        'images', // Must be fillable for storing JSON image IDs
    ];

    // Data type casting
    protected $casts = [
        'date_of_entry' => 'date',
        'images' => 'array', // Stores image IDs as a JSON array
    ];

    /**
     * Relationship: Belongs to User
     * Links each work progress entry to the user who created it.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Belongs to Project
     * Links work progress to a specific sub-package project.
     */
    public function project()
    {
        return $this->belongsTo(SubPackageProject::class, 'project_id');
    }

    /**
     * Relationship: Belongs to Work Component
     * Connects this progress record to its defined work component.
     */
    public function workComponent()
    {
        return $this->belongsTo(AlreadyDefinedWorkProgress::class, 'work_component_id');
    }

    /**
     * Relationship: Has many Media Files
     * Fetches all media files whose IDs are stored in the images field.
     * This assumes that 'images' contains an array of MediaFile IDs.
     */
    public function mediaFiles()
    {
        return $this->hasMany(MediaFile::class, 'id', 'images');
    }
}
