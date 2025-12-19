<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function routes()
    {
        return $this->hasMany(PermissionGroupRoute::class, 'group_id');
    }
}
