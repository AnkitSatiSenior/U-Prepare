<?php

// app/Models/GeographyDivision.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeographyDivision extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug'];

    public function districts(): HasMany
    {
        return $this->hasMany(GeographyDistrict::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(GeographyBlock::class);
    }
}