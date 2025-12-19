<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrievanceComplaintDetail extends Model
{
    use HasFactory;

    protected $table = 'grievance_complaint_detail';

    protected $fillable = [
        'nature_id',
        'name',
        'slug',
    ];

    /**
     * Relationship: Each detail belongs to one nature.
     */
    public function nature()
    {
        return $this->belongsTo(GrievanceComplaintNature::class, 'nature_id');
    }
}
