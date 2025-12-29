<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SubPackageProject;
use App\Models\EpcEntryData;
use App\Models\WorkService;
use App\Models\AlreadyDefineEpc;

class EpcEntryDataController extends Controller
{
    /**
     * Compute amount from contract value and percent (2-decimal rounding).
     */
    protected function calcAmount(?float $contractValue, ?float $percent): float
    {
        if (!$contractValue || !$percent) return 0.0;
        return round(($contractValue * $percent) / 100, 2);
    }

    /**
     * Display EPC entries with validation warnings and metadata.
     */
    public function index(Request $request)
    {
        $subProjects   = SubPackageProject::select('id', 'name', 'contract_value')->get();
        $workservices  = WorkService::select('id', 'name')->get();

        $selectedWorkServiceId = $request->input('work_service_id');
        $selectedProjectId     = $request->input('sub_package_project_id');

        // IMPORTANT: keep these names to match Blade
        $epcentrydefine   = collect();
        $epcEntries       = collect();
        $subProject       = null;
        $remainingPercent = null;
        $remainingAmount  = null;
        $warnings         = [];

        // Predefined definitions for selected work service
        if ($selectedWorkServiceId) {
            $epcentrydefine = AlreadyDefineEpc::with(['activityName:id,name', 'workService:id,name'])
                ->where('work_service', $selectedWorkServiceId)
                ->orderBy('sl_no')
                ->get();
        }

        // EPC entries for selected project
        if ($selectedProjectId) {
            $subProject = SubPackageProject::find($selectedProjectId);

            $epcEntries = EpcEntryData::where('sub_package_project_id', $selectedProjectId)
                ->orderByRaw("CAST(SUBSTRING_INDEX(sl_no, '.', 1) AS UNSIGNED), sl_no")
                ->get()
                ->groupBy(fn($item) => explode('.', $item->sl_no)[0]);

            // Validation & warnings
            $totalPercent = EpcEntryData::where('sub_package_project_id', $selectedProjectId)->sum('percent');
            if ($totalPercent !== 100) {
                $remainingPercent = 100 - $totalPercent;
                $warnings[] = $totalPercent > 100
                    ? "Total percentage exceeds 100% by " . abs($remainingPercent) . "%."
                    : "Total percentage is less than 100% by " . abs($remainingPercent) . "%.";
            }

            if ($subProject?->contract_value) {
                $totalAmount = EpcEntryData::where('sub_package_project_id', $selectedProjectId)->sum('amount');
                $remainingAmount = $subProject->contract_value - $totalAmount;
                $warnings[] = $totalAmount > $subProject->contract_value
                    ? "Total amount exceeds contract value by " . number_format(abs($remainingAmount), 2) . "."
                    : "Total amount is less than contract value by " . number_format(abs($remainingAmount), 2) . ".";
            }
        }

        return view('admin.epcentry_data.index', compact(
            'subProjects',
            'workservices',
            'epcentrydefine',
            'epcEntries',
            'subProject',
            'selectedWorkServiceId',
            'selectedProjectId',
            'remainingPercent',
            'remainingAmount',
            'warnings'
        ));
    }

    /**
     * Store EPC entries from predefined template (AlreadyDefineEpc).
     * - Auto-distributes amounts from contract_value
     * - Skips duplicates by (sub_package_project_id, sl_no)
     */
    public function storeFromDefined(Request $request)
    {
        $validated = $request->validate([
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'work_service_id'        => 'required|exists:work_service,id',
        ]);

        $subProject     = SubPackageProject::findOrFail($validated['sub_package_project_id']);
        $workServiceId  = $validated['work_service_id'];

        $defined = AlreadyDefineEpc::with('activityName:id,name')
            ->where('work_service', $workServiceId)
            ->orderBy('sl_no')
            ->get();

        if ($defined->isEmpty()) {
            return back()->with('error', 'No defined EPC entries found for this work service.');
        }

        // Fetch existing sl_no for this project to avoid duplicates
        $existingSlNos = EpcEntryData::where('sub_package_project_id', $subProject->id)
            ->pluck('sl_no')
            ->map(fn($s) => (string) $s)
            ->toArray();

        DB::beginTransaction();
        try {
            $insertData = [];

            foreach ($defined as $def) {
                $sl = (string) $def->sl_no;
                if (in_array($sl, $existingSlNos, true)) {
                    // Skip existing sl_no
                    continue;
                }

                $insertData[] = [
                    'sub_package_project_id' => $subProject->id,
                    'sl_no'                  => $sl,
                    'activity_name'          => $def->activityName?->name,
                    'stage_name'             => $def->stage_name,
                    'item_description'       => $def->item_description,
                    'percent'                => $def->percent ?? 0,
                    'amount'                 => $this->calcAmount($subProject->contract_value, $def->percent),
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ];
            }

            if (empty($insertData)) {
                DB::rollBack();
                return back()->with('info', 'All defined EPC entries already exist for this project.');
            }

            EpcEntryData::insert($insertData);
            DB::commit();

            return redirect()
                ->route('admin.epcentry_data.index', [
                    'sub_package_project_id' => $subProject->id,
                    'work_service_id'        => $workServiceId
                ])
                ->with('success', count($insertData) . " EPC entries stored successfully from defined entries.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("EPC StoreFromDefined failed", [
                'project_id' => $subProject->id,
                'work_service_id' => $workServiceId,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to store EPC entries. Please try again.');
        }
    }

    /**
     * Show create form.
     */
    public function create(Request $request)
    {
        $subProject = $request->has('sub_package_project_id')
            ? SubPackageProject::findOrFail($request->sub_package_project_id)
            : null;

        return view('admin.epcentry_data.create', compact('subProject'));
    }

    /**
     * Store single or multiple EPC entries.
     * Auto-computes amount if not provided but percent + contract_value exist.
     */
    public function store(Request $request)
    {
        $isBulk = $request->has('entries') && is_array($request->entries);

        $validated = $isBulk
            ? $request->validate([
                'sub_package_project_id'     => 'required|exists:sub_package_projects,id',
                'entries'                    => 'required|array|min:1',
                'entries.*.sl_no'            => 'required|string|max:255',
                'entries.*.activity_name'    => 'required|string',
                'entries.*.stage_name'       => 'nullable|string',
                'entries.*.item_description' => 'nullable|string',
                'entries.*.percent'          => 'nullable|numeric|min:0|max:100',
                'entries.*.amount'           => 'nullable|numeric|min:0',
            ])
            : $request->validate([
                'sub_package_project_id' => 'required|exists:sub_package_projects,id',
                'sl_no'                  => 'required|string|max:255',
                'activity_name'          => 'required|string',
                'stage_name'             => 'nullable|string',
                'item_description'       => 'nullable|string',
                'percent'                => 'nullable|numeric|min:0|max:100',
                'amount'                 => 'nullable|numeric|min:0',
            ]);

        $subProject = SubPackageProject::findOrFail($validated['sub_package_project_id']);

        DB::beginTransaction();
        try {
            if ($isBulk) {
                // Optional: prevent duplicate sl_no in same payload
                $payloadSlNos = collect($validated['entries'])->pluck('sl_no')->map(fn($s) => (string)$s);
                if ($payloadSlNos->duplicates()->isNotEmpty()) {
                    return back()->with('error', 'Duplicate SL No found in submitted entries.');
                }

                // Skip sl_no that already exist in DB for this sub project
                $existingSlNos = EpcEntryData::where('sub_package_project_id', $subProject->id)
                    ->pluck('sl_no')->map(fn($s) => (string)$s)->toArray();

                $insertData = [];
                foreach ($validated['entries'] as $entry) {
                    $sl = (string) $entry['sl_no'];
                    if (in_array($sl, $existingSlNos, true)) {
                        continue; // skip duplicates
                    }

                    // Auto amount if missing
                    $amount = $entry['amount'] ?? $this->calcAmount($subProject->contract_value, $entry['percent'] ?? null);

                    $insertData[] = [
                        'sub_package_project_id' => $validated['sub_package_project_id'],
                        'sl_no'                  => $sl,
                        'activity_name'          => $entry['activity_name'],
                        'stage_name'             => $entry['stage_name'] ?? null,
                        'item_description'       => $entry['item_description'] ?? null,
                        'percent'                => $entry['percent'] ?? 0,
                        'amount'                 => $amount,
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ];
                }

                if (empty($insertData)) {
                    DB::rollBack();
                    return back()->with('info', 'All submitted entries already exist for this project.');
                }

                EpcEntryData::insert($insertData);
                $count = count($insertData);
            } else {
                $data = $validated;
                $data['amount'] = $data['amount'] ?? $this->calcAmount($subProject->contract_value, $data['percent'] ?? null);
                EpcEntryData::create($data);
                $count = 1;
            }

            DB::commit();
            return redirect()
                ->route('admin.epcentry_data.index', ['sub_package_project_id' => $validated['sub_package_project_id']])
                ->with('success', "$count EPC entr" . ($count > 1 ? 'ies' : 'y') . " created successfully.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("EPC Store failed", [
                'project_id' => $validated['sub_package_project_id'],
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to create EPC entries. Please try again.');
        }
    }

    /**
     * Edit EPC entry form.
     */
    public function edit($id)
    {
        $entry       = EpcEntryData::findOrFail($id);
        $subProjects = SubPackageProject::select('id', 'name')->get();
        return view('admin.epcentry_data.edit', compact('entry', 'subProjects'));
    }

    /**
     * Update EPC entry.
     * Auto-recomputes amount if not provided but percent/contract_value present.
     */
    public function update(Request $request, $id)
    {
        $entry = EpcEntryData::findOrFail($id);

        $validated = $request->validate([
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'sl_no'                  => 'required|string|max:255',
            'activity_name'          => 'required|string',
            'stage_name'             => 'nullable|string',
            'item_description'       => 'nullable|string',
            'percent'                => 'nullable|numeric|min:0|max:100',
            'amount'                 => 'nullable|numeric|min:0',
        ]);

        try {
            $subProject = SubPackageProject::findOrFail($validated['sub_package_project_id']);

            // Only auto-calc amount if not explicitly provided
            if (!array_key_exists('amount', $validated) || $validated['amount'] === null) {
                $validated['amount'] = $this->calcAmount($subProject->contract_value, $validated['percent'] ?? null);
            }

            $entry->update($validated);

            return redirect()
                ->route('admin.epcentry_data.index', ['sub_package_project_id' => $validated['sub_package_project_id']])
                ->with('success', 'EPC entry updated successfully.');
        } catch (\Throwable $e) {
            Log::error("EPC Update failed", [
                'entry_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to update EPC entry. Please try again.');
        }
    }

    /**
     * Bulk destroy EPC entries (permanent).
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'exists:epcentry_data,id',
        ]);

        try {
            // Permanent delete (use withTrashed if model uses SoftDeletes)
            $count = EpcEntryData::withTrashed()->whereIn('id', $validated['ids'])->forceDelete();

            $message = "$count EPC entr" . ($count > 1 ? 'ies' : 'y') . " permanently deleted.";
            return $request->ajax()
                ? response()->json(['message' => $message])
                : redirect()->route('admin.epcentry_data.index')->with('success', $message);
        } catch (\Throwable $e) {
            Log::error("EPC bulk delete failed", [
                'ids' => $validated['ids'],
                'error' => $e->getMessage(),
            ]);
            return $request->ajax()
                ? response()->json(['error' => 'Failed to delete EPC entries.'], 500)
                : back()->withErrors('Error deleting entries.');
        }
    }

    /**
     * Soft delete single EPC entry.
     */
    public function destroy($id)
    {
        $entry     = EpcEntryData::findOrFail($id);
        $projectId = $entry->sub_package_project_id;

        try {
            $entry->delete();
            return redirect()
                ->route('admin.epcentry_data.index', ['sub_package_project_id' => $projectId])
                ->with('success', 'EPC entry deleted (soft delete).');
        } catch (\Throwable $e) {
            Log::error("EPC Delete failed", [
                'entry_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to delete EPC entry.');
        }
    }
}
