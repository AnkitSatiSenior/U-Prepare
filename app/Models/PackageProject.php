<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};

class PackageProject extends Model
{
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */
    const STATUS_PENDING_PROCUREMENT = 'Pending for Procurement';
    const STATUS_PENDING_CONTRACT = 'Pending for Contract';
    const STATUS_PENDING_ACTIVITY = 'Pending for Physical Activity';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_CANCEL = 'Cancel';
    const STATUS_REBID = 'To be Rebid';
    const STATUS_REMOVED = 'Removed';

    /*
    |--------------------------------------------------------------------------
    | Fillable Columns
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'project_id',
        'package_category_id',
        'package_sub_category_id',
        'department_id',
        'sub_department_id',
        'package_component_id',
        'package_name',
        'package_number',
        'estimated_budget_incl_gst',
        'vidhan_sabha_id',
        'lok_sabha_id',
        'district_id',
        'block_id',
        'dec_approved',
        'dec_approval_date',
        'dec_letter_number',
        'dec_document_path',
        'hpc_approved',
        'hpc_approval_date',
        'hpc_letter_number',
        'hpc_document_path',
        'status',
        'safeguard_exists', // ✅ new column
    ];

    /*
    |--------------------------------------------------------------------------
    | Type Casting
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'dec_approved' => 'boolean',
        'hpc_approved' => 'boolean',
        'estimated_budget_incl_gst' => 'decimal:2',
        'dec_approval_date' => 'date',
        'hpc_approval_date' => 'date',
        'safeguard_exists' => 'boolean', // ✅ cast safeguard flag
    ];

    /*
    |--------------------------------------------------------------------------
    | Global Scopes: Role-Based Access
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        if (app()->runningInConsole()) {
            return;
        }

        $user = Auth::user();

        if ($user && $user->role_id !== 1) {
            static::addGlobalScope('userAssignments', function (Builder $builder) use ($user) {
                $builder->whereHas('assignments', function ($q) use ($user) {
                    $q->where('assigned_to', $user->id);
                });
            });
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeBasicInfo($query)
    {
        return $query->select('id', 'package_name', 'status', 'safeguard_exists');
    }

    public function scopeWithWorkProgramData($query)
    {
        return $query->with(['procurementDetail', 'workPrograms', 'contracts']);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        return $query
            ->when($filters['department_id'] ?? null, fn($q, $v) => $q->where('department_id', $v))
            ->when($filters['sub_department_id'] ?? null, fn($q, $v) => $q->where('sub_department_id', $v))
            ->when($filters['package_component_id'] ?? null, fn($q, $v) => $q->where('package_component_id', $v))
            ->when($filters['district_id'] ?? null, fn($q, $v) => $q->where('district_id', $v))
            ->when($filters['category_id'] ?? null, fn($q, $v) => $q->where('package_category_id', $v))
            ->when($filters['sub_category_id'] ?? null, fn($q, $v) => $q->where('package_sub_category_id', $v))
            ->when($filters['vidhan_sabha_id'] ?? null, fn($q, $v) => $q->where('vidhan_sabha_id', $v))
            ->when($filters['lok_sabha_id'] ?? null, fn($q, $v) => $q->where('lok_sabha_id', $v))
            ->when($filters['block_id'] ?? null, fn($q, $v) => $q->where('block_id', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('package_name', 'like', "%{$search}%")->orWhere('package_number', 'like', "%{$search}%");
                });
            })
            ->when($filters['has_contract'] ?? null, function ($q, $value) {
                if ($value == 1) {
                    $q->whereHas('contracts');
                } elseif ($value == 0) {
                    $q->whereDoesntHave('contracts');
                }
            })
            ->when($filters['has_safeguard'] ?? null, function ($q, $value) {
                if ($value == 1) {
                    $q->where('safeguard_exists', true);
                } elseif ($value == 0) {
                    $q->where('safeguard_exists', false);
                }
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectsCategory::class, 'package_category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'package_sub_category_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function subDepartment(): BelongsTo
    {
        return $this->belongsTo(SubDepartment::class, 'sub_department_id');
    }

    public function packageComponent(): BelongsTo
    {
        return $this->belongsTo(PackageComponent::class, 'package_component_id');
    }

    public function vidhanSabha(): BelongsTo
    {
        return $this->belongsTo(Constituency::class, 'vidhan_sabha_id');
    }

    public function lokSabha(): BelongsTo
    {
        return $this->belongsTo(Constituency::class, 'lok_sabha_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(GeographyDistrict::class);
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(GeographyBlock::class);
    }

    public function procurementDetail(): HasOne
    {
        return $this->hasOne(ProcurementDetail::class);
    }

    public function workPrograms(): HasMany
    {
        return $this->hasMany(ProcurementWorkProgram::class);
    }

    public function subProjects(): HasMany
    {
        return $this->hasMany(SubPackageProject::class, 'project_id', 'id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'project_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(PackageProjectAssignment::class, 'package_project_id');
    }

    public function subprojectLinks(): HasMany
    {
        return $this->hasMany(ProjectSubprojectLink::class, 'project_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getHasWorkProgramAttribute(): bool
    {
        return $this->workPrograms->isNotEmpty();
    }

    public function getHasContractAttribute(): bool
    {
        return $this->contracts()->exists();
    }

    public function isEpc(): bool
    {
        return optional($this->procurementDetail?->typeOfProcurement)->name === 'EPC';
    }

    public function isBoq(): bool
    {
        return optional($this->procurementDetail?->typeOfProcurement)->name === 'item-wise';
    }
}
