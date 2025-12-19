<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grievance extends Model
{
    use HasFactory;

    protected $table = 'grievances';

    protected $fillable = [
        'grievance_no',
        'full_name',
        'address',
        'email',
        'mobile',
        'grievance_related_to',
        'nature_of_complaint',
        'detail_of_complaint',
        'district',
        'block',
        'village',
        'project',
        'description',
        'document',
        'filing_on_behalf',
        'consent_from_survivor',
        'status',
    ];

    /**
     * Relationship: Logs related to grievance
     */
    public function logs()
    {
        return $this->hasMany(GrievanceLog::class);
    }

    /**
     * Relationship: Assignments related to grievance
     */
    public function assignments()
    {
        return $this->hasMany(GrievanceAssignment::class);
    }

    /**
     * Relationship: User who created the grievance
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
