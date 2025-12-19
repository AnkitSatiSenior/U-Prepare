<?php

// app/Models/GeographyDistrict.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeographyDistrict extends Model
{
    use SoftDeletes;

    protected $fillable = ['division_id', 'name', 'slug'];

    public function division(): BelongsTo
    {
        return $this->belongsTo(GeographyDivision::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(GeographyBlock::class);
    }
}