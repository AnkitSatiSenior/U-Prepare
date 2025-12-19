<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractionPhase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contraction_phases';

    protected $fillable = [
        'name',
        'is_one_time',
    ];

    protected $casts = [
        'is_one_time' => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    /**
     * Relationship: One Phase → Many Safeguard Entries
     */
    public function safeguardEntries()
    {
        return $this->hasMany(AlreadyDefineSafeguardEntry::class, 'contraction_phase_id');
    }
}
