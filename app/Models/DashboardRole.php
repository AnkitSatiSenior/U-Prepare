<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DashboardRole extends Model
{
    use HasFactory;

    protected $table = 'dashboard_roles'; // make sure matches DB table name

    protected $fillable = [
        'role_id',
        'department',
    ];

    // Relation: DashboardRole belongs to Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public static function userHasAccess($user, $departmentId = null): bool
{
    $dashboardRole = self::where('role_id', $user->role_id)->first();

    if (!$dashboardRole) {
        return false;
    }

    // ✅ Department 8 has full access always
    if ($user->department_id == 8) {
        return true;
    }

    // ✅ "all" means access to everything
    if ($dashboardRole->department === 'all') {
        return true;
    }

    // ✅ "department" means access only to same department
    if ($dashboardRole->department === 'department' && $departmentId) {
        return $user->department_id == $departmentId;
    }

    return false;
}

}
