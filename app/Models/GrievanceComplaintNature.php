<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrievanceComplaintNature extends Model
{
    use HasFactory;

    protected $table = 'grievance_complaint_nature';

    protected $fillable = [
        'name',
        'slug',
    ];
    public function details() {
    return $this->hasMany(GrievanceComplaintDetail::class, 'nature_id');
}
}
