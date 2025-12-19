<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Contractor;
use App\Models\PackageProject;
use App\Models\SubPackageProject;
use App\Models\EpcEntryData;
use App\Models\BoqEntryData;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        // Load all contracts with relationships
        $allContracts = Contract::with(['project:id,package_name', 'contractor:id,company_name', 'securities.type:id,name', 'securities.form:id,name'])
            ->select('id', 'is_updated', 'contract_number', 'project_id', 'contractor_id', 'contract_value', 'count_sub_project', 'signing_date')
            ->latest()
            ->get();

        $now = now();
        $oneMonthLater = now()->addMonth();

        // Process securities for all contracts
        $allContracts->each(function ($contract) use ($now, $oneMonthLater) {
            $contract->active_securities = $contract->securities->filter(fn($sec) => !$sec->issued_end_date || $sec->issued_end_date >= $oneMonthLater);
            $contract->expired_securities = $contract->securities->filter(fn($sec) => $sec->issued_end_date && $sec->issued_end_date < $now);
            $contract->expiring_soon_securities = $contract->securities->filter(fn($sec) => $sec->issued_end_date && $sec->issued_end_date >= $now && $sec->issued_end_date < $oneMonthLater);
        });

        // Counts for cards (always based on all contracts)
        $counts = [
            'total_contracts' => $allContracts->count(),
            'active' => $allContracts->sum(fn($c) => $c->active_securities->count()),
            'expired' => $allContracts->sum(fn($c) => $c->expired_securities->count()),
            'expiring_soon' => $allContracts->sum(fn($c) => $c->expiring_soon_securities->count()),
        ];

        // Get filter from request
        $filter = $request->get('filter');

        // Filter contracts based on selected filter
        $filteredContracts = match ($filter) {
            'active' => $allContracts->filter(fn($c) => $c->active_securities->count() > 0),
            'expired' => $allContracts->filter(fn($c) => $c->expired_securities->count() > 0),
            'expiring_soon' => $allContracts->filter(fn($c) => $c->expiring_soon_securities->count() > 0),
            'total_contracts', null => $allContracts, // show all contracts
            default => $allContracts,
        };

        // Get package projects
        if (is_null($filter)) {
            // No filter: show all package projects
            $packageProjects = PackageProject::withWorkProgramData()->latest()->get();
        } else {
            // Filter applied: show only projects linked to filtered contracts
            $packageProjects = PackageProject::withWorkProgramData()
                ->whereHas('contracts', function ($query) use ($filteredContracts) {
                    $query->whereIn('id', $filteredContracts->pluck('id'));
                })
                ->latest()
                ->get();
        }

        return view('admin.contracts.index', [
            'contracts' => $filteredContracts,
            'packageProjects' => $packageProjects,
            'filter' => $filter,
            'counts' => $counts,
        ]);
    }

    public function create(Request $request)
    {
        $projects = PackageProject::select('id', 'package_name', 'package_number')->orderBy('package_name')->get();

        $contractors = Contractor::select('id', 'company_name', 'gst_no')->orderBy('company_name')->get();

        $packageProjects = PackageProject::basicInfo()->get();

        $contract = new Contract();
        $contract->contractor = new Contractor();
        $selectedPackageProjectId = null;

        if ($request->package_project_id) {
            // ✅ Check if a contract already exists
            $exists = Contract::where('project_id', $request->package_project_id)->exists();
            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'A contract has already been created for the selected project.');
            }

            // Only fetch if not already used
            $selectedPackageProjectId = PackageProject::withWorkProgramData()->find($request->package_project_id);
        }

        return view('admin.contracts.create', compact('projects', 'contractors', 'contract', 'selectedPackageProjectId', 'packageProjects'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateContract($request);

        try {
            DB::transaction(function () use ($validated, $request) {
                // Create contractor if not selected
                if (empty($validated['contractor_id']) && isset($validated['contractor'])) {
                    $validated['contractor_id'] = Contractor::create($validated['contractor'])->id;
                }

                // Handle file upload
                if ($request->hasFile('contract_document_file')) {
                    $validated['contract_document'] = $this->storeContractFile($request->file('contract_document_file'));
                }

                unset($validated['contractor'], $validated['contract_document_file']);

                // Create contract
                $contract = Contract::create($validated);

                $contract->load('project');

                // Handle sub-package project logic
                $this->handleSubProjects($contract, $request); // ✅ Pass both arguments
            });

            return redirect()->route('admin.contracts.index')->with('success', 'Contract created successfully.');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create contract: ' . $e->getMessage()]);
        }
    }
    public function show($id)
    {
        $contract = Contract::with(['project.procurementDetail.typeOfProcurement', 'contractor', 'subProjects'])->findOrFail($id);

        // Format contract numbers and dates
        $contract->formatted_value = number_format($contract->contract_value, 2);
        $contract->formatted_security = number_format($contract->security ?? 0, 2);
        $contract->formatted_signing_date = optional($contract->signing_date)->format('d M Y') ?? 'N/A';
        $contract->formatted_commencement_date = optional($contract->commencement_date)->format('d M Y') ?? 'N/A';
        $contract->formatted_initial_completion_date = optional($contract->initial_completion_date)->format('d M Y') ?? 'N/A';
        $contract->formatted_revised_completion_date = optional($contract->revised_completion_date)->format('d M Y') ?? 'N/A';

        // Get type name safely, default to 'EPC'
        $procurementTypeName = $contract->project->procurementDetail->typeOfProcurement->name ?? 'EPC';
        $procurementType = strtolower(trim($procurementTypeName));

        $subProjectsData = $contract->subProjects->map(function ($sp) use ($procurementType) {
            $actions = [];

            // 1. FINANCIAL PROGRESS
            $financeTotal = \App\Models\FinancialProgressUpdate::where('project_id', $sp->id)->sum('finance_amount');
            $financePercent = $sp->contract_value > 0 ? round(($financeTotal / $sp->contract_value) * 100, 2) : 0;

            // 2. PHYSICAL PROGRESS
            $physicalValue = 0;
            $physicalPercent = 0;

            if ($procurementType === 'epc') {
                $physicalValue = \App\Models\PhysicalEpcProgress::whereHas('epcEntryData', function ($q) use ($sp) {
                    $q->where('sub_package_project_id', $sp->id);
                })->sum('amount');

                $physicalPercent = $sp->contract_value > 0 ? round(($physicalValue / $sp->contract_value) * 100, 2) : 0;
            } elseif ($procurementType === 'item-rate') {
                $physicalValue = \App\Models\PhysicalBoqProgress::where('sub_package_project_id', $sp->id)->sum('amount');

                $physicalPercent = $sp->contract_value > 0 ? round(($physicalValue / $sp->contract_value) * 100, 2) : 0;
            }

            // 3. ACTION BUTTONS
            if ($procurementType === 'epc') {
                $epcEntries = \App\Models\EpcEntryData::where('sub_package_project_id', $sp->id)->get();
                $totalPercent = $epcEntries->sum('percent');
                $totalAmount = $epcEntries->sum('amount');

                $hasCompleteEpcEntries = $epcEntries->count() > 0 && $totalPercent == 100;
                $amountMatchesContractValue = $totalAmount == $sp->contract_value;

                if ($hasCompleteEpcEntries) {
                    $actions[] = [
                        'label' => 'Modify Activities',
                        'icon' => 'fas fa-industry',
                        'class' => 'btn-success',
                        'route' => route('admin.epcentry_data.index', ['sub_package_project_id' => $sp->id]),
                    ];

                    if ($amountMatchesContractValue) {
                        $actions[] = [
                            'label' => 'Update Physical Progress',
                            'icon' => 'fas fa-hard-hat',
                            'class' => 'btn-info',
                            'route' => route('admin.physical_epc_progress.index', ['sub_package_project_id' => $sp->id]),
                        ];
                    }
                } else {
                    $actions[] = [
                        'label' => 'Define Activities',
                        'icon' => 'fas fa-industry',
                        'class' => 'btn-success',
                        'route' => route('admin.epcentry_data.index', ['sub_package_project_id' => $sp->id]),
                    ];
                }
            } elseif ($procurementType === 'item-rate') {
                $boqEntries = \App\Models\BoqEntryData::where('sub_package_project_id', $sp->id)->get();
                $totalAmount = $boqEntries->sum('amount');

                if ($boqEntries->count() > 0) {
                    $actions[] = [
                        'label' => 'Update BOQ Sheet',
                        'icon' => 'fas fa-file-invoice-dollar',
                        'class' => 'btn-primary',
                        'route' => route('admin.boqentry.index', ['sub_package_project_id' => $sp->id]),
                    ];

                    if ($totalAmount == $sp->contract_value) {
                        $actions[] = [
                            'label' => 'Update Physical Progress',
                            'icon' => 'fas fa-hard-hat',
                            'class' => 'btn-info',
                            'route' => route('admin.physical_itemwise_progress.index', ['sub_package_project_id' => $sp->id]),
                        ];
                    }
                } else {
                    $actions[] = [
                        'label' => 'Create BOQ Sheet',
                        'icon' => 'fas fa-file-invoice-dollar',
                        'class' => 'btn-primary',
                        'route' => route('admin.boqentry.index', ['sub_package_project_id' => $sp->id]),
                    ];
                }
            } else {
                // Default actions if unknown type
                $actions[] = [
                    'label' => 'Create BOQ Sheet',
                    'icon' => 'fas fa-file-invoice-dollar',
                    'class' => 'btn-primary',
                    'route' => route('admin.boqentry.index', ['sub_package_project_id' => $sp->id]),
                ];
                $actions[] = [
                    'label' => 'Create EPC Entry',
                    'icon' => 'fas fa-industry',
                    'class' => 'btn-success',
                    'route' => route('admin.epcentry_data.index', ['sub_package_project_id' => $sp->id]),
                ];
            }

            // Always add Financial Progress Update

            // Always add Safe Guard Entry
            $actions[] = [
                'label' => 'Create Environment Details',
                'icon' => 'fas fa-shield-alt',
                'class' => 'btn-warning',
                'route' => route('admin.safeguard_entries.index', ['sub_package_project_id' => $sp->id]),
            ];

            return [
                'id' => $sp->id,
                'name' => $sp->name,
                'contractValue' => number_format($sp->contract_value, 2),
                'financeTotal' => number_format($financeTotal, 2),
                'financePercent' => $financePercent,
                'physicalValue' => number_format($physicalValue, 2),
                'physicalPercent' => $physicalPercent,
                'actions' => $actions,
            ];
        });

        return view('admin.contracts.show', [
            'contract' => $contract,
            'subProjectsData' => $subProjectsData,
        ]);
    }

    public function edit(Contract $contract)
    {
        // Load main relationships
        $contract->load([
            'project',
            'contractor',
            'subProjects.packageProject', // nested for clarity
        ]);

        // Fetch dropdown data
        $projects = PackageProject::select('id', 'package_name', 'package_number')->get();
        $contractors = Contractor::select('id', 'company_name', 'gst_no')->get();

        // Fetch all SubPackageProjects linked to the same project_id
        $subProjects = SubPackageProject::with(['packageProject:id,package_name,package_number', 'procurementDetail.typeOfProcurement:id,name'])
            ->where('project_id', $contract->project_id)
            ->select('id', 'project_id', 'name', 'contract_value')
            ->get();

        return view('admin.contracts.edit', compact('contract', 'projects', 'contractors', 'subProjects'));
    }
    public function editsubpackge(Contract $contract)
    {
        // Load main relationships
        $contract->load([
            'project',
            'contractor',
            'subProjects.packageProject', // nested for clarity
        ]);

        // Fetch dropdown data
        $projects = PackageProject::select('id', 'package_name', 'package_number')->get();
        $contractors = Contractor::select('id', 'company_name', 'gst_no')->get();

        // Fetch all SubPackageProjects linked to the same project_id
        $subProjects = SubPackageProject::with(['packageProject:id,package_name,package_number', 'procurementDetail.typeOfProcurement:id,name'])
            ->where('project_id', $contract->project_id)
            ->select('id', 'project_id', 'name', 'contract_value')
            ->get();

        return view('admin.contracts.subpackge', compact('contract', 'projects', 'contractors', 'subProjects'));
    }
    public function updateMultiple(Request $request)
    {
        $data = $request->input('sub_projects', []);

        foreach ($data as $id => $fields) {
            $subProject = SubPackageProject::find($id);

            if ($subProject) {
                $subProject->update([
                    'name' => $fields['name'] ?? $subProject->name,
                    'contract_value' => $fields['contract_value'] ?? 0,
                    'lat' => $fields['lat'] ?? null,
                    'long' => $fields['long'] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'All sub-projects updated successfully!');
    }

    public function update(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        // Track old values
        $oldValues = $contract->only(['contract_value', 'initial_completion_date', 'actual_completion_date']);

        // Track new incoming values
        $newValues = $request->only(['contract_value', 'initial_completion_date', 'actual_completion_date']);

        // Check if anything changed
        $isChanged = $oldValues['contract_value'] != $newValues['contract_value'] || $oldValues['initial_completion_date'] != $newValues['initial_completion_date'] || $oldValues['actual_completion_date'] != $newValues['actual_completion_date'];

        // ✅ Validation rules
        $rules = [
            'contract_number' => ['required', 'string', Rule::unique('contracts', 'contract_number')->ignore($id)],
            'contract_value' => 'required|numeric|min:0',
            'security' => 'nullable|numeric|min:0',
            'count_sub_project' => 'nullable|numeric|min:0',
            'signing_date' => 'nullable|date',
            'commencement_date' => 'nullable|date',
            'initial_completion_date' => 'nullable|date',
            'revised_completion_date' => 'nullable|date',
            'actual_completion_date' => 'nullable|date',
            'contractor_id' => 'required|exists:contractors,id',
        ];

        // 🔒 If changed → document required
        if ($isChanged) {
            $rules['update_document_file'] = 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:25120';
        } else {
            $rules['update_document_file'] = 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:25120';
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($request, $contract, $oldValues, $newValues, $isChanged) {
            // Update contract
            $contract->update($request->only(['contract_number', 'contract_value', 'security', 'signing_date', 'commencement_date', 'initial_completion_date', 'revised_completion_date', 'actual_completion_date', 'count_sub_project']));

            // If values changed, log update
            if ($isChanged) {
                $documentPath = null;

                if ($request->hasFile('update_document_file')) {
                    $file = $request->file('update_document_file');
                    $fileName = 'update_' . time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                    $documentPath = $file->storeAs('contract_updates', $fileName, 'public');
                }

                $contract->updates()->create([
                    'old_contract_value' => $oldValues['contract_value'],
                    'new_contract_value' => $newValues['contract_value'],
                    'old_initial_completion_date' => $oldValues['initial_completion_date'],
                    'new_initial_completion_date' => $newValues['initial_completion_date'],
                    'old_actual_completion_date' => $oldValues['actual_completion_date'],
                    'new_actual_completion_date' => $newValues['actual_completion_date'],
                    'changed_at' => now(),
                    'update_document' => $documentPath, // ✅ save file path
                ]);

                $contract->increment('update_count');
                $contract->update(['is_updated' => true]);
            }
        });

        return redirect()->route('admin.contracts.index')->with('success', 'Contract updated successfully.');
    }

    public function history($id)
    {
        $contract = Contract::with([
            'updates' => function ($q) {
                $q->latest();
            },
        ])->findOrFail($id);

        return view('admin.contracts.history', compact('contract'));
    }

    public function destroy(Contract $contract)
    {
        try {
            DB::transaction(function () use ($contract) {
                $this->deleteContractFile($contract->contract_document);
                $contract->delete();
            });

            return redirect()->route('admin.contracts.index')->with('success', 'Contract deleted successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete contract: ' . $e->getMessage()]);
        }
    }

    /**
     * Create or update sub-package projects and sync count.
     */
    private function handleSubProjects(Contract $contract, Request $request)
    {
        // Remove old sub-projects for this contract's project
        SubPackageProject::where('project_id', $contract->project_id)->delete();

        $isMulti = $request->input('has_multiple_sub_projects') === 'yes';
        $contractValue = (float) $contract->contract_value;

        if (!$isMulti) {
            // Single sub-project mode
            $name = $request->input('sub_project_name') ?: $contract->project->package_name;
            $value = $request->input('sub_project_contract_value') ?: $contractValue;
            $lat = $request->input('lat'); // Nullable lat
            $long = $request->input('long'); // Nullable long

            SubPackageProject::create([
                'name' => $name,
                'contract_value' => $value,
                'project_id' => $contract->project_id,
                'lat' => $lat,
                'long' => $long,
            ]);
        } else {
            // Multiple sub-project mode
            $multiData = $request->input('multi_sub_projects', []);
            $count = max(count($multiData), 2); // Ensure at least 2

            // Default value per project if missing
            $defaultValue = $count > 0 ? round($contractValue / $count, 2) : 0;

            foreach ($multiData as $i => $sp) {
                SubPackageProject::create([
                    'name' => $sp['name'] ?: 'Sub Project ' . ($i + 1),
                    'contract_value' => isset($sp['value']) && $sp['value'] !== '' ? $sp['value'] : $defaultValue,
                    'project_id' => $contract->project_id,
                    'lat' => $sp['lat'] ?? null,
                    'long' => $sp['long'] ?? null,
                ]);
            }
        }

        // Update count on contract
        $contract->update([
            'count_sub_project' => SubPackageProject::where('project_id', $contract->project_id)->count(),
        ]);
    }

    private function storeContractFile($file)
    {
        $fileName = 'contract_' . time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        return $file->storeAs('contracts', $fileName, 'public');
    }

    private function deleteContractFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function validateContract(Request $request, $id = null)
    {
        $rules = [
            'contract_number' => ['required', 'string', Rule::unique('contracts', 'contract_number')->ignore($id)],
            'project_id' => 'required|exists:package_projects,id',
            'contract_value' => 'required|numeric|min:0',
            'security' => 'nullable|numeric|min:0',
            'count_sub_project' => 'nullable|numeric|min:0',
            'signing_date' => 'nullable|date',
            'commencement_date' => 'nullable|date',
            'initial_completion_date' => 'nullable|date',
            'revised_completion_date' => 'nullable|date',
            'actual_completion_date' => 'nullable|date',
            'contractor_id' => $id ? 'required|exists:contractors,id' : 'nullable|exists:contractors,id',
            'contract_document_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:25120',

            // Single sub-project lat/long (nullable, numeric)
            'lat' => 'nullable|numeric|between:-90,90',
            'long' => 'nullable|numeric|between:-180,180',

            // For multiple sub-projects: validate each item's lat/long as nullable numeric
            'multi_sub_projects.*.lat' => 'nullable|numeric|between:-90,90',
            'multi_sub_projects.*.long' => 'nullable|numeric|between:-180,180',
        ];

        if (!$id && !$request->filled('contractor_id')) {
            $rules = array_merge($rules, [
                'contractor.company_name' => 'required_without:contractor_id|string|max:255',
                'contractor.authorized_personnel_name' => 'required_without:contractor_id|string|max:255',
                'contractor.phone' => 'nullable|string|max:20|unique:contractors,phone',
                'contractor.email' => 'nullable|email|unique:contractors,email',
                'contractor.gst_no' => 'nullable|string|max:50|unique:contractors,gst_no',
                'contractor.address' => 'nullable|string|max:500',
            ]);
        }

        $messages = [
            'contractor.company_name.required_without' => 'Company Name is required if no contractor is selected.',
            'contractor.authorized_personnel_name.required_without' => 'Authorized Personnel Name is required if no contractor is selected.',
        ];

        return $request->validate($rules, $messages);
    }
}
