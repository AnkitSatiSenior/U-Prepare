<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EpcEntryData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'epcentry_data';

    protected $fillable = [
        'sub_package_project_id',
        'sl_no',
        'activity_name',    // changed from item_description
        'stage_name',       // newly added
        'item_description', // newly added column included here
        'percent',
        'amount',
    ];

    public function subPackageProject()
    {
        return $this->belongsTo(SubPackageProject::class, 'sub_package_project_id');
    }

    // ✅ Add this relationship
    public function physicalEpcProgresses()
    {
        return $this->hasMany(PhysicalEpcProgress::class, 'epcentry_data_id');
    }
}
