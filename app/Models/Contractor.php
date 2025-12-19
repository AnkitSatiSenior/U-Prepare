<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contractor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'authorized_personnel_name',
        'phone',
        'email',
        'gst_no',
        'address',
    ];

protected $casts = [
    'phone' => 'string',
    'email' => 'string',
];

    // A Contractor can have many Contracts
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
