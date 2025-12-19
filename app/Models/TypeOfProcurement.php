<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeOfProcurement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description'];

    // Optional: reverse relation
    public function procurementDetails()
    {
        return $this->hasMany(ProcurementDetail::class);
    }

    public function packageProjects()
    {
        return $this->hasManyThrough(
            PackageProject::class,
            ProcurementDetail::class,
            'type_of_procurement_id', // FK on procurement_details
            'id', // PK on package_projects
            'id', // PK on type_of_procurements
            'package_project_id', // FK on procurement_details
        );
    }
}
