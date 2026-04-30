<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'username', 'role_id', 'department_id', 
        'sub_department_id', 'designation_id', 'gender', 'phone_no', 'status', 
        'district', 'profile_photo_path',
        'dob', 'date_of_joining', 'qualification', 'total_work_experience',
        'area_of_expertise', 'procurement_support', 'research_publication_citation',
        'previous_experience',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret'
    ];

    // ✅ Jetstream expects exactly this naming convention
    protected $appends = ['profile_photo_url'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'date_of_joining' => 'date',
        ];
    }

    /** ------------------------------
     * Relationships
     * ------------------------------
     */

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user');
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

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /** ------------------------------
     * Helpers & Accessors
     * ------------------------------
     */

    public function canAccess($route)
    {
        return $this->role->routes->contains('route_name', $route);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Overrides Jetstream's native profile photo URL resolver.
     * ✅ ARCHITECTURE FIX: Replaced local asset() with robust S3 try/catch resolution.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        $fallback = asset('images/demo-user.png');

        if (empty($this->profile_photo_path)) {
            return $fallback;
        }

        try {
            return Storage::disk('s3')->url($this->profile_photo_path);
        } catch (Exception $e) {
            Log::error('Failed to resolve S3 URL for User Profile Photo', [
                'user_id' => $this->id,
                'path' => $this->profile_photo_path,
                'error' => $e->getMessage()
            ]);
            
            return $fallback;
        }
    }
    /**
     * Scope to find targeted users for Escalation mapping.
     */
    public function scopeMappedToEscalation($query, array $levels, int $subPackageId, int $complianceId)
    {
        return $query->where(function ($q) use ($levels) {
            // Must meet the Level requirement via Role OR Designation
            $q->whereHas('role', fn($r) => $r->whereIn('level', $levels))
              ->orWhereHas('designation', fn($d) => $d->whereIn('level', $levels));
        })
        // Must be explicitly mapped in the pivot table for this EXACT violation
        ->whereExists(function ($query) use ($subPackageId, $complianceId) {
            $query->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('user_safeguard_subpackage as uss')
                  ->whereColumn('uss.user_id', 'users.id')
                  ->where('uss.sub_package_project_id', $subPackageId)
                  ->where('uss.safeguard_compliance_id', $complianceId)
                  ->whereNull('uss.deleted_at'); 
        });
    }
}
