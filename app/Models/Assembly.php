<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assembly extends Model
{
    use SoftDeletes;

    protected $table = 'assembly';
    
    protected $fillable = [
        'district_id',
        'constituency_id',
        'name',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(GeographyDistrict::class, 'district_id');
    }

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class, 'constituency_id');
    }
}