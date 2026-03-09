<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // ✅ Required for Jetstream Email Verification
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail // ✅ Implementation Added
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'username', 
        'role_id', 
        'department_id', 
        'sub_department_id', 
        'designation_id', 
        'gender', 
        'phone_no', 
        'status', 
        'district', 
        'profile_photo_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** -------------------------------------------------------------------
     * Relationships
     * -------------------------------------------------------------------
     */

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

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

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user');
    }

    /** -------------------------------------------------------------------
     * Helpers & Logic
     * -------------------------------------------------------------------
     */

    /**
     * Check if user has a specific role by name.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user can access a specific route based on role permissions.
     */
    public function canAccess($route): bool
    {
        return $this->role && $this->role->routes->contains('route_name', $route);
    }

    /**
     * Custom Accessor for Profile Photo. 
     * Overrides Jetstream default to provide a custom fallback.
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path 
            ? asset('storage/' . $this->profile_photo_path) 
            : asset('images/demo-user.png');
    }
}