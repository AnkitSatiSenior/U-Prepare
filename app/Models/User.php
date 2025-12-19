<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable, SoftDeletes;

    protected $fillable = ['name', 'email', 'password', 'username', 'role_id', 'department_id', 'sub_department_id', 'designation_id', 'gender', 'phone_no', 'status', 'district', 'profile_photo_path'];

    protected $hidden = ['password', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret'];

    protected $appends = ['profile_photo_url'];
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
    public function canAccess($route)
    {
        return $this->role->routes->contains('route_name', $route);
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** ------------------------------
     *  Relationships
     *  ------------------------------
     */

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function subDepartment()
    {
        return $this->belongsTo(SubDepartment::class, 'sub_department_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /** ------------------------------
     *  Helpers
     *  ------------------------------
     */

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path ? asset('storage/' . $this->profile_photo_path) : asset('images/demo-user.png');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }
}
