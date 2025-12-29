<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_number',
        'project_id',
        'contract_value',
        'security',
        'signing_date',
        'commencement_date',
        'initial_completion_date',
        'revised_completion_date',
        'actual_completion_date',
        'contract_document',
        'count_sub_project',
        'contractor_id',
        'is_updated',
        'update_count',
    ];

    protected $casts = [
        'signing_date'              => 'datetime',
        'commencement_date'         => 'datetime',
        'initial_completion_date'   => 'datetime',
        'revised_completion_date'   => 'datetime',
        'actual_completion_date'    => 'datetime',
        'is_updated'                => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function project()
    {
        return $this->belongsTo(PackageProject::class, 'project_id');
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }

    public function subProjects()
    {
        return $this->hasMany(SubPackageProject::class, 'project_id', 'project_id');
    }

    public function securities()
    {
        return $this->hasMany(ContractSecurity::class);
    }

    public function active_securities()
    {
        return $this->hasMany(ContractSecurity::class)->where('issued_end_date', '>=', now());
    }

    public function expired_securities()
    {
        return $this->hasMany(ContractSecurity::class)->where('issued_end_date', '<', now());
    }

    public function updates()
    {
        return $this->hasMany(ContractUpdate::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeWithBasicRelations($query)
    {
        return $query->with([
            'project:id,package_name,department_id',
            'project.department:id,name',
            'contractor:id,company_name',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors / Custom Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Determine contract status (ongoing, completed, delayed, not started).
     */
    public function getStatusAttribute(): string
    {
        $today = now();

        if ($this->actual_completion_date) {
            return 'completed';
        }

        if ($this->commencement_date && $this->commencement_date > $today) {
            return 'not started';
        }

        $completionDate = $this->revised_completion_date ?? $this->initial_completion_date;

        if ($completionDate && $completionDate < $today) {
            return 'delayed';
        }

        if ($this->commencement_date && (!$completionDate || $completionDate >= $today)) {
            return 'ongoing';
        }

        return 'pending';
    }

    /**
     * Log contract changes into contract_updates table.
     */
    public function logUpdate(array $oldValues, array $newValues): void
{
    $this->updates()->create([
        'old_contract_value'           => $oldValues['contract_value'] ?? null,
        'new_contract_value'           => $newValues['contract_value'] ?? null,
        'old_initial_completion_date'  => $oldValues['initial_completion_date'] ?? null,
        'new_initial_completion_date'  => $newValues['initial_completion_date'] ?? null,
        'old_revised_completion_date'  => $oldValues['revised_completion_date'] ?? null,
        'new_revised_completion_date'  => $newValues['revised_completion_date'] ?? null,
        'old_actual_completion_date'   => $oldValues['actual_completion_date'] ?? null,
        'new_actual_completion_date'   => $newValues['actual_completion_date'] ?? null,
        'changed_at'                   => now(),
    ]);

    $this->increment('update_count');
    $this->update(['is_updated' => true]);
}

   public function generateMilestones(): array
{
    if (!$this->commencement_date) {
        return [];
    }

    $startDate = $this->commencement_date->copy()->startOfDay();

    // --- Step 0: Determine old & new end dates correctly ---
    if ($this->is_updated && $latestUpdate = $this->updates()->latest('changed_at')->first()) {
        $oldEndDate = Carbon::parse($latestUpdate->old_initial_completion_date)->endOfDay();
        $newEndDate = Carbon::parse($latestUpdate->new_revised_completion_date ?? $latestUpdate->new_initial_completion_date ?? $this->revised_completion_date ?? $this->initial_completion_date)->endOfDay();
    } else {
        $oldEndDate = $this->initial_completion_date->copy()->endOfDay();
        $newEndDate = $this->revised_completion_date ?? $oldEndDate;
    }

    // --- Step 1: Split old duration into 3 equal milestones ---
    $milestones = $this->splitIntoMilestones($startDate, $oldEndDate, 3);

    // --- Step 2: Extend last milestone if new end is later ---
    if ($newEndDate->gt($oldEndDate)) {
        $lastIndex = count($milestones) - 1;
        $milestones[$lastIndex]['to'] = $newEndDate->copy();
        $milestones[$lastIndex]['months'] = round(
            $milestones[$lastIndex]['from']->floatDiffInMonths($newEndDate) + 1,
            2
        );
        $milestones[$lastIndex]['label'] = 'Extended ' . $milestones[$lastIndex]['label'];
    }

    return $milestones;
}


    /**
     * Helper: Split duration into equal milestones.
     */
    private function splitIntoMilestones(Carbon $start, Carbon $end, int $parts = 3): array
    {
        $totalDays = $start->diffInDays($end) + 1;
        $baseDays = intdiv($totalDays, $parts);
        $extra = $totalDays % $parts;

        $segments = [];
        $cur = $start->copy();

        for ($i = 0; $i < $parts; $i++) {
            $days = $baseDays + ($i < $extra ? 1 : 0);
            $segEnd = $cur->copy()->addDays($days - 1);

            $segments[] = [
                'label' => 'M' . ($i + 1),
                'from' => $cur->copy(),
                'to' => $segEnd->copy(),
                'months' => round($cur->floatDiffInMonths($segEnd) + 1, 2),
            ];

            $cur = $segEnd->copy()->addDay();
        }

        return $segments;
    }
}
