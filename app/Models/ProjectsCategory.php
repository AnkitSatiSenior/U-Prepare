<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectsCategory extends Model
{
    use SoftDeletes;

    protected $table = 'projects_category';

    protected $fillable = [
        'name',
        'methods_of_procurement',
        'status',
    ];

   
protected $casts = [
    'methods_of_procurement' => 'array',
    'status' => 'boolean'
];
}
