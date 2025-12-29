<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ContractSecurity extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'security_type_id', 'security_form_id', 'issue_date', 'issued_end_date', 'security_number', 'bank_name', 'value', 'file_path'];

   

    // Append new computed attribute
    protected $appends = ['is_expired', 'is_valid_period', 'is_near_expiry'];

    // Security is near expiry (less than or equal to 1 month left)
    public function getIsNearExpiryAttribute()
    {
        if ($this->issued_end_date) {
            $end = Carbon::parse($this->issued_end_date);
            return !$this->is_expired && now()->diffInDays($end, false) <= 30;
        }
        return false;
    }

    // Relationships
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function type()
    {
        return $this->belongsTo(ContractSecurityType::class, 'security_type_id');
    }

    public function form()
    {
        return $this->belongsTo(ContractSecurityForm::class, 'security_form_id');
    }

    // Check if security has expired (end date < today)
    public function getIsExpiredAttribute()
    {
        if ($this->issued_end_date) {
            return now()->greaterThan(Carbon::parse($this->issued_end_date));
        }
        return false;
    }

    // Check if security is within contract period
    public function getIsValidPeriodAttribute()
    {
        if (!$this->contract) {
            return true;
        }

        $contractStart = $this->contract->commencement_date;
        $contractEnd = $this->contract->revised_completion_date ?? ($this->contract->actual_completion_date ?? $this->contract->initial_completion_date);

        if ($this->issue_date && $this->issued_end_date) {
            return $this->issue_date >= $contractStart && $this->issued_end_date <= $contractEnd;
        }

        return true;
    }
}
