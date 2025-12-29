<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GrievanceLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    'grievance_id',
    'user_id',
    'type', // preliminary | final | log
    'title',
    'remark',
    'document',
    'created_at', // allow mass assignment if needed
];


    public function grievance()
    {
        return $this->belongsTo(Grievance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
