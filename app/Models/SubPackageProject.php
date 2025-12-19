<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubPackageProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['project_id', 'name', 'contract_value', 'lat', 'long', 'safeguard_exists'];

    protected $casts = [
        'lat' => 'float',
        'long' => 'float',
        'contract_value' => 'decimal:2',

        'safeguard_exists' => 'boolean', // ✅ cast safeguard flag
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function workProgressData()
    {
        return $this->hasMany(WorkProgressData::class, 'project_id');
    }
  public function socialSafeguardProgress(?int $complianceId = null, ?Carbon $requestedStart = null, ?Carbon $requestedEnd = null): array
{
    // Resolve dates
    $contractStart = $this->packageProject?->contracts()->latest('id')->first()?->commencement_date;
    $start = $requestedStart ? Carbon::parse($requestedStart) : ($contractStart ? Carbon::parse($contractStart) : $this->created_at ?? now());
    $end = $requestedEnd ? Carbon::parse($requestedEnd) : now();

    if ($contractStart && $start->lt(Carbon::parse($contractStart))) $start = Carbon::parse($contractStart);
    if ($end->gt(now())) $end = now();
    if ($start->gt($end)) [$start, $end] = [$end, $start];

    $startMonth = $start->copy()->startOfMonth();
    $endMonth = $end->copy()->endOfMonth();
    $monthsInRange = max(1, (int) $startMonth->diffInMonths($endMonth) + 1);

    $progress = [];

    // Fetch master entries (already_define_safeguard_entries) instead of old safeguard_entries
    $masterEntriesQuery = AlreadyDefineSafeguardEntry::with('safeguardCompliance', 'contractionPhase');
    if ($complianceId) {
        $masterEntriesQuery->where('safeguard_compliance_id', $complianceId);
    }
    $masterEntries = $masterEntriesQuery->orderBy('order_by')->get();

    // Group by compliance
    $grouped = $masterEntries->groupBy('safeguard_compliance_id');

    foreach ($grouped as $cid => $entries) {
        $compliance = $entries->first()->safeguardCompliance;

        // Group by phase
        $phaseGroups = $entries->groupBy('contraction_phase_id');

        $phaseReports = [];
        $totalAll = 0;
        $doneAll = 0;

        foreach ($phaseGroups as $phaseId => $phaseEntries) {
            $phase = $phaseEntries->first()->contractionPhase;

            $masterEntryIds = $phaseEntries->pluck('id')->toArray();
            $childCount = count($masterEntryIds);

            if ($childCount === 0) {
                $phaseReports[] = [
                    'id' => $phaseId,
                    'phase' => $phase?->name ?? 'N/A',
                    'total' => 0,
                    'done' => 0,
                    'entry_count' => 0,
                    'percent' => 0,
                    'is_one_time' => (bool) ($phase?->is_one_time ?? false),
                ];
                continue;
            }

            // Total expected
            $effectiveMonths = $phase?->is_one_time ? 1 : $monthsInRange;
            $totalForPhase = $childCount * $effectiveMonths;

            // Done entries from social_safeguard_entries
            $doneForPhase = DB::table('social_safeguard_entries')
                ->whereIn('already_define_safeguard_entry_id', $masterEntryIds)
                ->where('sub_package_project_id', $this->id)
                ->where('social_compliance_id', $cid)
                ->where('contraction_phase_id', $phaseId)
                ->when(!$phase?->is_one_time, fn($q) => $q->whereBetween('date_of_entry', [$startMonth->toDateString(), $endMonth->toDateString()]))
                ->whereIn('yes_no', [1, 3])
                ->select(DB::raw("COUNT(DISTINCT CONCAT(already_define_safeguard_entry_id, '-', DATE_FORMAT(date_of_entry, '%Y-%m'))) as cnt"))
                ->value('cnt') ?? 0;

            // Entry count
            $entryCount = DB::table('social_safeguard_entries')
                ->whereIn('already_define_safeguard_entry_id', $masterEntryIds)
                ->where('sub_package_project_id', $this->id)
                ->where('social_compliance_id', $cid)
                ->where('contraction_phase_id', $phaseId)
                ->when(!$phase?->is_one_time, fn($q) => $q->whereBetween('date_of_entry', [$startMonth->toDateString(), $endMonth->toDateString()]))
                ->count();

            $percent = $totalForPhase > 0 ? round(($doneForPhase / $totalForPhase) * 100, 2) : 0.0;

            $phaseReports[] = [
                'id' => $phaseId,
                'phase' => $phase?->name ?? 'N/A',
                'total' => $totalForPhase,
                'done' => $doneForPhase,
                'entry_count' => $entryCount,
                'percent' => $percent,
                'is_one_time' => (bool) ($phase?->is_one_time ?? false),
            ];

            $totalAll += $totalForPhase;
            $doneAll += $doneForPhase;
        }

        $progress[$cid] = [
            'compliance' => $compliance->name ?? 'N/A',
            'phases' => $phaseReports,
            'total' => $totalAll,
            'done' => $doneAll,
            'percent' => $totalAll > 0 ? round(($doneAll / $totalAll) * 100, 2) : 0,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'monthsInRange' => $monthsInRange,
        ];
    }

    return $progress;
}



    public function workProgress()
    {
        return $this->hasMany(WorkProgressData::class, 'project_id');
    }
    public function getWorkServiceIdAttribute()
    {
        return $this->packageProject?->package_category_id;
    }

    public function packageProject()
    {
        return $this->belongsTo(PackageProject::class, 'project_id');
    }

    public function procurementDetail()
    {
        return $this->hasOneThrough(
            ProcurementDetail::class,
            PackageProject::class,
            'id', // FK on PackageProject
            'package_project_id', // FK on ProcurementDetail
            'project_id', // FK on SubPackageProject
            'id', // PK on PackageProject
        );
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'project_id', 'project_id');
    }

    public function safeguardEntries()
    {
        return $this->hasMany(SafeguardEntry::class);
    }

    public function financialProgressUpdates()
    {
        return $this->hasMany(FinancialProgressUpdate::class, 'project_id');
    }

    // ------------------- EPC -------------------
    public function epcEntries()
    {
        return $this->hasMany(EpcEntryData::class, 'sub_package_project_id');
    }

    public function physicalEpcProgresses()
    {
        return $this->hasManyThrough(
            PhysicalEpcProgress::class,
            EpcEntryData::class,
            'sub_package_project_id', // FK on EpcEntryData
            'epcentry_data_id', // FK on PhysicalEpcProgress
            'id', // PK on SubPackageProject
            'id', // PK on EpcEntryData
        );
    }

    // ------------------- BOQ -------------------
    public function boqEntries()
    {
        return $this->hasMany(BoqEntryData::class, 'sub_package_project_id');
    }

    public function physicalBoqProgresses()
    {
        return $this->hasManyThrough(
            PhysicalBoqProgress::class,
            BoqEntryData::class,
            'sub_package_project_id', // FK on BoqEntryData
            'boq_entry_id', // FK on PhysicalBoqProgress
            'id', // PK on SubPackageProject
            'id', // PK on BoqEntryData
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Procurement Type Helper
    |--------------------------------------------------------------------------
    */
    public function getTypeOfProcurementNameAttribute()
    {
        return $this->procurementDetail ? $this->procurementDetail->typeOfProcurement->name : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Physical Progress Accessors
    |--------------------------------------------------------------------------
    */
    public function physicalProgressPercentage(): float
    {
        return $this->type_of_procurement_name === 'EPC' ? $this->calculateEpcProgress() : $this->calculateBoqProgressWithGST();
    }

    public function getPhysicalProgressPercentageAttribute(): float
    {
        return $this->physicalProgressPercentage();
    }

    protected function calculateEpcProgress(): float
    {
        $plannedAmount = $this->epcEntries()->sum('amount');
        $executedAmount = $this->physicalEpcProgresses()->selectRaw('COALESCE(SUM(physical_epc_progress.amount),0) as total')->value('total');

        return $plannedAmount > 0 ? round(($executedAmount / $plannedAmount) * 100, 2) : 0.0;
    }

    protected function calculateBoqProgressWithGST(): float
    {
        $plannedAmount = $this->boqEntries()->sum('amount');
        $executedAmount = $this->physicalBoqProgresses()->selectRaw('COALESCE(SUM(physical_boq_progress.amount),0) as total')->value('total');

        // Include 18% GST
        $plannedWithGST = $plannedAmount * 1.18;
        $executedWithGST = $executedAmount * 1.18;

        return $plannedWithGST > 0 ? round(($executedWithGST / $plannedWithGST) * 100, 2) : 0.0;
    }

    public function getBoqProgressWithGstAttribute(): float
    {
        return $this->calculateBoqProgressWithGST();
    }

    /*
    |--------------------------------------------------------------------------
    | Finance Accessors
    |--------------------------------------------------------------------------
    */
    public function getTotalFinanceAmountAttribute(): float
    {
        return $this->financialProgressUpdates()->sum('finance_amount');
    }

    public function getFinancialProgressPercentageAttribute(): float
    {
        return $this->contract_value > 0 ? round(($this->total_finance_amount / $this->contract_value) * 100, 2) : 0.0;
    }

    /*
    |--------------------------------------------------------------------------
    | Data Presence Checks
    |--------------------------------------------------------------------------
    */
    public function getHasEntryDataAttribute(): bool
    {
        return $this->type_of_procurement_name === 'EPC' ? $this->epcEntries()->exists() : $this->boqEntries()->exists();
    }

    public function getHasPhysicalProgressAttribute(): bool
    {
        return $this->type_of_procurement_name === 'EPC' ? $this->physicalEpcProgresses()->exists() : $this->physicalBoqProgresses()->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Tests Relationship
    |--------------------------------------------------------------------------
    */
    public function tests()
    {
        return $this->hasMany(SubPackageProjectTest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Boot Method (Global Scope)
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::addGlobalScope('userAssignments', function (Builder $builder) {
            if (auth()->check() && auth()->user()->role_id !== 1) {
                $builder->whereHas('packageProject.assignments', function ($q) {
                    $q->where('assigned_to', auth()->id());
                });
            }
        });
    }
    public function projectLinks()
    {
        return $this->hasMany(ProjectSubprojectLink::class, 'subproject_id');
    }
}
