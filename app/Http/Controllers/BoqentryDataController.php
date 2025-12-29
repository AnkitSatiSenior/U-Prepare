<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\BoqentryDataImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SubPackageProject;
use App\Models\BoqEntryData;

class BoqentryDataController extends Controller
{
    public function index(Request $request)
    {
        $subProjects = SubPackageProject::select('id', 'name')->get();

        $selectedProjectId = $request->input('sub_package_project_id');

        $boqEntries = collect();
        $subProject = null;

        if ($selectedProjectId) {
            $subProject = SubPackageProject::find($selectedProjectId);

            $boqEntries = BoqEntryData::where('sub_package_project_id', $selectedProjectId)
                ->orderByRaw("CAST(SUBSTRING_INDEX(sl_no, '.', 1) AS UNSIGNED), sl_no")
                ->get()
                ->groupBy(function ($item) {
                    return explode('.', $item->sl_no)[0];
                });
        }

        return view('admin.boqentry.index', compact('subProjects', 'boqEntries', 'subProject', 'selectedProjectId'));
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new BoqentryDataImport($request->sub_package_project_id), $request->file('excel_file'));

        return redirect()
            ->route('admin.boqentry.index', ['sub_package_project_id' => $request->sub_package_project_id])
            ->with('success', 'BOQ entries imported successfully.');
    }

    // Show form to edit a BOQ entry
    public function edit($id)
    {
        $entry = BoqEntryData::findOrFail($id);
        $subProjects = SubPackageProject::select('id', 'name')->get();

        return view('admin.boqentry.edit', compact('entry', 'subProjects'));
    }

    // Update BOQ entry
    public function update(Request $request, $id)
    {
        $entry = BoqEntryData::findOrFail($id);

        $validated = $request->validate([
            'sl_no' => 'required|string|max:20',
            'item_description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'qty' => 'nullable|numeric',
            'rate' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
        ]);

        // Optionally format sl_no like 2.2 -> 2.20 here again

        $entry->update($validated);

        return redirect()
            ->route('admin.boqentry.index', ['sub_package_project_id' => $entry->sub_package_project_id])
            ->with('success', 'BOQ entry updated successfully.');
    }
    // Show create form
    public function create(Request $request)
    {
        $subProjects = SubPackageProject::select('id', 'name')->get();

        // Optionally pre-select a project if sent as query param, e.g. ?sub_package_project_id=3
        $selectedProjectId = $request->query('sub_package_project_id');

        return view('admin.boqentry.create', compact('subProjects', 'selectedProjectId'));
    }

    // Store new BOQ entry
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'sl_no' => 'required|string|max:20',
            'item_description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'qty' => 'nullable|numeric',
            'rate' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
        ]);

        // Optional: format sl_no here as needed (e.g., add trailing zero)

        BoqEntryData::create($validated);

        return redirect()
            ->route('admin.boqentry.index', ['sub_package_project_id' => $validated['sub_package_project_id']])
            ->with('success', 'BOQ entry created successfully.');
    }
    // Bulk delete BOQ entries
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:boqentry_data,id',
        ]);

        $ids = $request->input('ids');

        try {
            BoqEntryData::whereIn('id', $ids)->delete();

            if ($request->ajax()) {
                return response()->json([
                    'message' => count($ids) . ' BOQ entries deleted successfully.',
                ]);
            }

            return redirect()
                ->route('admin.boqentry.index')
                ->with('success', count($ids) . ' BOQ entries deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Bulk delete failed for BOQ entries IDs: ' . implode(',', $ids) . '. Error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(
                    [
                        'error' => 'Failed to delete BOQ entries: ' . $e->getMessage(),
                    ],
                    500,
                );
            }

            return redirect()
                ->route('admin.boqentry.index')
                ->withErrors('Error deleting entries: ' . $e->getMessage());
        }
    }

    // Delete BOQ entry
    public function destroy($id)
    {
        $entry = BoqEntryData::findOrFail($id);
        $projectId = $entry->sub_package_project_id;
        $entry->delete();

        return redirect()
            ->route('admin.boqentry.index', ['sub_package_project_id' => $projectId])
            ->with('success', 'BOQ entry deleted successfully.');
    }
}
