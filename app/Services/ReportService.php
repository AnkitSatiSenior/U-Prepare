<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Http\Request;

class ReportService
{
    public function getReportData()
    {
        $departments = Department::query()
            ->withProjectAndContractStats()
            ->withFinancialStats()
            ->with([
                'projects' => function ($q) {
                    $q->with([
                        'category:id,name',
                        'subCategory:id,name',
                        'subProjects:id,project_id,name,contract_value', // include value if stored here
                        'contracts:id,project_id,contract_number,contract_value',
                        'district:id,name',
                        'block:id,name',
                        'procurementDetail:id,project_id,method_of_procurement',
                    ]);
                },
            ])
            ->get();

        $packageProjects = $departments->flatMap(fn($d) => $d->projects)->unique('id')->values();

        $stats = [
            'total_departments' => $departments->count(),
            'total_projects' => $departments->sum('projects_count'),
            'signed_contracts' => $departments->sum('signed_contracts_count'),
            'total_contract_value' => $departments->sum('contracts_sum_contract_value'),
            'total_finance' => $departments->sum('financials_sum_finance_amount'),
        ];

        return compact('departments', 'packageProjects', 'stats');
    }

    public function buildSubProjectsData($subProjects, string $procurementType, Request $request)
    {
        return $subProjects->map(function ($sp) use ($procurementType) {
            // Contract value
            $contractValue = $sp->contract_value ?? (optional(optional($sp->project)->contracts->first())->contract_value ?? 0);

            // Finance updates
            $financeUpdates = $sp->financialProgressUpdates ?? collect();
            $financeAchieved = $financeUpdates->sum('finance_amount'); // adjust column if diff
            $financeLastDate = $financeUpdates->max('submit_date');

            // Physical progress
            $physicalEpc = $sp->physicalEpcProgresses ?? collect();
            $physicalBoq = $sp->physicalBoqProgresses ?? collect();
            $physicalLastDate = $physicalEpc->max('date') ?? $physicalBoq->max('date');

            $physicalAchieved = $physicalEpc->avg('progress_percent') ?? ($physicalBoq->avg('progress_percent') ?? 0);

            // Safe % calculation
            $financePercent = $contractValue > 0 ? round(($financeAchieved / $contractValue) * 100, 2) : 0;
            $physicalPercent = $physicalAchieved;

            return [
                'id' => $sp->id,
                'name' => $sp->name,
                'financeUpdates' => $financeUpdates,
                'financeEntries' => $financeUpdates, // ✅ alias for Blade
                'physicalEpc' => $physicalEpc,
                'physicalBoq' => $physicalBoq,
                'safeguards' => $sp->safeguards ?? collect(),
                'procurementType' => $procurementType,
                'contractValue' => $contractValue,
                'financePercent' => $financePercent,
                'physicalPercent' => $physicalPercent,
                'financeLastDate' => $financeLastDate,
                'physicalLastDate' => $physicalLastDate,
            ];
        });
    }

    public function flattenFinanceEntries($subProjectsData)
    {
        return $subProjectsData->flatMap(fn($sp) => $sp['financeUpdates'] ?? collect())->sortBy('submit_date')->values();
    }
}
