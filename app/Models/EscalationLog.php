<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Add this import

class EscalationLog extends Model
{
    protected $table = 'escalation_logs';

    protected $fillable = [
        'escalatable_id',
        'escalatable_type',
        'compliance_id',
        'day_mark',
        'level',
        'type',
    ];

    /**
     * Get the parent escalatable model (e.g., SubPackageProject).
     */
    public function escalatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the associated Safeguard Compliance category.
     */
    public function compliance(): BelongsTo
    {
        // Links the compliance_id in this table to the id in safeguard_compliances
        return $this->belongsTo(SafeguardCompliance::class, 'compliance_id');
    }
}