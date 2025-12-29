<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoqEntryData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'boqentry_data';

    protected $fillable = ['sub_package_project_id', 'sl_no', 'item_description', 'unit', 'qty', 'rate', 'amount'];

    public function subPackageProject()
    {
        return $this->belongsTo(SubPackageProject::class, 'sub_package_project_id');
    }
    // App\Models\BoqEntryData.php
    public function physicalBoqProgresses()
    {
        return $this->hasMany(PhysicalBoqProgress::class, 'boq_entry_id', 'id');
    }
    public function variations()
    {
        return $this->hasMany(BoqentryVariation::class, 'boqentry_id');
    }
}
