<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafeguardCompliance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'role_id',
        'contraction_phase_ids', // stored as JSON
    ];

    protected $casts = [
        'contraction_phase_ids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Role relation
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get contraction phases as a collection
     */
    public function getContractionPhasesAttribute()
    {
        return ContractionPhase::whereIn('id', $this->contraction_phase_ids ?? [])
            ->orderBy('name') // optional ordering
            ->get();
    }
    public function contractionPhases()
    {
        return $this->belongsToMany(
            ContractionPhase::class,
            'safeguard_entries', // pivot table name
            'safeguard_compliance_id', // foreign key on pivot
            'contraction_phase_id', // related key on pivot
        );
    }
}
