<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubPackageProjectTest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sub_package_project_id',
        'test_type_id',
        'test_name',
        'test_type',
        'status', // Add this if you want to track Completed/Pending
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Belongs to SubPackageProject
    public function subPackageProject()
    {
        return $this->belongsTo(SubPackageProject::class);
    }

    // Belongs to Test Type
    public function testType()
    {
        return $this->belongsTo(SubPackageProjectTestType::class, 'test_type_id');
    }

    // Has many reports
    public function reports()
    {
        return $this->hasMany(SubPackageProjectTestReport::class, 'test_id');
    }

    // Get latest report
    public function report()
    {
        return $this->hasOne(SubPackageProjectTestReport::class, 'test_id')->latestOfMany();
    }
}
