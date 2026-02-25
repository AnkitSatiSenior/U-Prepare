<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcurementDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'package_project_id',
        'type_of_procurement_id',
        'method_of_procurement',
        'publication_date',
        'publication_document_path',
        'technical_eval_date',
        'technical_eval_document_path',
        'financial_eval_date',
        'financial_eval_document_path',
        'loa_issued_date',
        'loa_issued_document_path',
        'tender_fee',
        'earnest_money_deposit',
        'bid_validity_days',
        'emd_validity_days',
    ];

    protected $casts = [
        'publication_date'       => 'date',
        'technical_eval_date'    => 'date',
        'financial_eval_date'    => 'date',
        'loa_issued_date'        => 'date',
        'tender_fee'             => 'decimal:2',
        'earnest_money_deposit'  => 'decimal:2',
        'bid_validity_days'      => 'integer',
        'emd_validity_days'      => 'integer',
    ];

    // Relation to PackageProject
    public function packageProject()
    {
        return $this->belongsTo(PackageProject::class);
    }

    // Relation to TypeOfProcurement
    public function typeOfProcurement()
    {
        return $this->belongsTo(TypeOfProcurement::class);
    }
}