<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'old_contract_value',
        'new_contract_value',
        'old_initial_completion_date',
        'new_initial_completion_date',
        'old_actual_completion_date',
        'new_actual_completion_date',
        'changed_at',
        'update_document', // ✅ added here
    ];

    protected $casts = [
        'old_initial_completion_date' => 'date',
        'new_initial_completion_date' => 'date',
        'old_actual_completion_date'  => 'date',
        'new_actual_completion_date'  => 'date',
        'changed_at'                  => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
