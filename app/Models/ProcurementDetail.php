<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcurementDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'package_project_id',
        'type_of_procurement_id', // use foreign key here
        'method_of_procurement',
        'publication_date',
        'publication_document_path',
        'tender_fee',
        'earnest_money_deposit',
        'bid_validity_days',
        'emd_validity_days',
    ];

    protected $casts = [
        'publication_date'       => 'date',
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
