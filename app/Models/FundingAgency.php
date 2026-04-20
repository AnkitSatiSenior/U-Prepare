<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundingAgency extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'code', 'is_active'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}