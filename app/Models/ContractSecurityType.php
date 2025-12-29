<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractSecurityType extends Model
{
    use HasFactory;

    protected $table = 'contract_security_types';

    protected $fillable = ['name'];
}
