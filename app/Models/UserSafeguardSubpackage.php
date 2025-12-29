<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSafeguardSubpackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_safeguard_subpackage';

    protected $fillable = [
        'user_id',
        'safeguard_compliance_id',
        'sub_package_project_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function safeguardCompliance()
    {
        return $this->belongsTo(SafeguardCompliance::class);
    }

    public function subPackageProject()
    {
        return $this->belongsTo(SubPackageProject::class);
    }
}
