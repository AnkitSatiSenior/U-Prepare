<?php

// app/Models/GeographyBlock.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeographyBlock extends Model
{
    use SoftDeletes;

    protected $fillable = ['division_id', 'district_id', 'name', 'slug'];

    public function division(): BelongsTo
    {
        return $this->belongsTo(GeographyDivision::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(GeographyDistrict::class);
    }
}
