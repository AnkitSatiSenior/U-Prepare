<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlreadyDefinedWorkProgress extends Model
{
    use HasFactory, SoftDeletes;

    // Table name
    protected $table = 'already_defined_work_progress';

    // Mass assignable fields
    protected $fillable = [
        'work_service_id',
        'work_component',
        'type_details',
        'side_location',
    ];

    /**
     * Relationship: Belongs to WorkService
     */
    public function workService()
    {
        return $this->belongsTo(WorkService::class, 'work_service_id');
    }

    /**
     * Relationship: Has many work progress entries
     */
    public function progressData()
    {
        return $this->hasMany(WorkProgressData::class, 'work_component_id');
    }
}
