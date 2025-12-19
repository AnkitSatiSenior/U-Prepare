<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoqentryVariation extends Model
{
    use HasFactory;

    protected $table = 'boqentry_variations';

    protected $fillable = [
        'boqentry_id',
        'old_qty',
        'new_qty',
        'old_rate',
        'new_rate',
        'old_amount',
        'new_amount',
        'changed_field',
        'remarks',
    ];

    /**
     * Relationship: Variation belongs to BOQ entry.
     */
    public function boq()
    {
        return $this->belongsTo(BoqentryData::class, 'boqentry_id');
    }
}
