<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Role has many SafeguardCompliances
     */
    public function safeguardCompliances()
    {
        return $this->hasMany(SafeguardCompliance::class, 'role_id');
    }

    /**
     * Role has many users
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    /**
     * Role has many routes
     */
    public function routes()
    {
        return $this->hasMany(RoleRoute::class, 'role_id');
    }
}
