<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageStatus extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'is_active',
        'order_by',
    ];

    // This ensures Laravel always treats 'is_active' as a true/false boolean
    protected $casts = [
        'is_active' => 'boolean', 
    ];
}