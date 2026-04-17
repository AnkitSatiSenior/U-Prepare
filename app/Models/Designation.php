<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Designation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'level',
    ];

    /**
     * The attributes that should be cast.
     * Ensures 'level' is always handled as an integer in the application logic.
     */
    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * Designation has many users.
     * * @return HasMany
     */
    public function users(): HasMany
    {
        // Explicitly defining the return type and foreign key for clarity
        return $this->hasMany(User::class, 'designation_id');
    }

    /**
     * Scope a query to sort by hierarchy level.
     */
    public function scopeHighestRank($query)
    {
        return $query->orderBy('level', 'desc');
    }
}