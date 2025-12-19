<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectSubprojectLink extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['project_id', 'subproject_id', 'user_id', 'remark'];

    /**
     * Main Project Relationship
     */
    public function project()
    {
        return $this->belongsTo(PackageProject::class, 'project_id');
    }

    /**
     * Sub Project Relationship
     */
    public function subproject()
    {
        return $this->belongsTo(SubPackageProject::class, 'subproject_id');
    }

    /**
     * User Relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
