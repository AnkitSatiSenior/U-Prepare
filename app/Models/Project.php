<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'loan_signing_date'        => 'date',
        'start_date'               => 'date',
        'scheduled_closure_date'   => 'date',
        'revised_closure_date'     => 'date',
        'is_revised'               => 'boolean',
        'is_dli_based'             => 'boolean',
        'implementation_locations' => 'array', // Stores Uttarakhand districts
    ];

    public function fundingAgency(): BelongsTo
    {
        return $this->belongsTo(FundingAgency::class, 'funding_agency_id');
    }
}