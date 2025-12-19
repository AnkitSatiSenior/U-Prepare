<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroupRoute extends Model
{
    protected $fillable = [
        'group_id',
        'route_name',
    ];

    public function group()
    {
        return $this->belongsTo(PermissionGroup::class, 'group_id');
    }
}
