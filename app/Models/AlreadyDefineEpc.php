<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlreadyDefineEpc extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'already_define_epc';

    protected $fillable = [
        'work_service',
        'sl_no',
        'activity_id',
        'stage_name',
        'item_description',
        'percent',
    ];

    // If you want to define relation to work_service model
    public function workService()
    {
        return $this->belongsTo(WorkService::class, 'work_service');
    }
    public function activityName()
{
    return $this->belongsTo(EpcActivityName::class, 'activity_id');
}

}
