<?php

namespace App\Http\Controllers;

use App\Models\SafeguardEntry;
use App\Models\SafeguardCompliance;
use App\Models\ContractionPhase;
use App\Models\SubPackageProject;
use App\Imports\SafeguardEntriesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SafeguardEntryController extends Controller
{
    /**
     * Display a listing of safeguard entries (with filters for project, compliance, and phase).
     */
   
    public function index(Request $request)
    {
        // Load all sub-projects for dropdowns
        $subProjects = SubPackageProject::select('id', 'name')->get();

        // Currently selected project
        $selectedProjectId = $request->input('sub_package_project_id');
        $subProject = null;
        $entries = collect();
        $safeguardCompliances = collect();
        $contractionPhases = collect();

        if ($selectedProjectId) {
            $subProject = SubPackageProject::findOrFail($selectedProjectId);

            // Build base query for entries in this project
            $query = SafeguardEntry::with(['safeguardCompliance', 'contractionPhase'])->where('sub_package_project_id', $selectedProjectId);

            // Apply filters if selected
            if ($request->filled('safeguard_compliance_id')) {
                $query->where('safeguard_compliance_id', $request->safeguard_compliance_id);
            }

            if ($request->filled('contraction_phase_id')) {
                $query->where('contraction_phase_id', $request->contraction_phase_id);
            }

            // Fetch and group entries
            $entries = $this->groupEntries($query->get());

            // Fetch only compliances relevant to this project
            $safeguardCompliances = SafeguardCompliance::all();

            // Fetch only phases relevant to this project
            $contractionPhases = ContractionPhase::all();
        }

        return view('admin.safeguard_entries.index', compact('subProjects', 'entries', 'subProject', 'selectedProjectId', 'safeguardCompliances', 'contractionPhases'));
    }

    /**
     * Import safeguard entries from an Excel/CSV file.
     */
    public function import(Request $request)
    {
        $this->validateImport($request);

        Excel::import(new SafeguardEntriesImport($request->sub_package_project_id, $request->safeguard_compliance_id, $request->contraction_phase_id), $request->file('file'));

        return back()->with('success', 'Safeguard entries imported successfully.');
    }

    /**
     * Show the form for creating a new safeguard entry.
     */
    public function create()
    {
        return view('admin.safeguard_entries.create', $this->formData());
    }

    /**
     * Store a newly created safeguard entry.
     */
    public function store(Request $request)
    {
        $validated = $this->validateEntry($request);
        $validated['is_validity'] = $request->boolean('is_validity', false);

        SafeguardEntry::create($validated);

        return redirect()
            ->route('admin.safeguard_entries.index', ['sub_package_project_id' => $validated['sub_package_project_id']])
            ->with('success', 'Safeguard entry created successfully.');
    }

    /**
     * Show the form for editing an existing safeguard entry.
     */
    public function edit(SafeguardEntry $safeguardEntry)
    {
        return view('admin.safeguard_entries.edit', array_merge(['safeguardEntry' => $safeguardEntry], $this->formData()));
    }

    /**
     * Update an existing safeguard entry.
     */
    public function update(Request $request, SafeguardEntry $safeguardEntry)
    {
        $validated = $this->validateEntry($request);
        $validated['is_validity'] = $request->boolean('is_validity', false);

        $safeguardEntry->update($validated);

        return redirect()
            ->route('admin.safeguard_entries.index', ['sub_package_project_id' => $validated['sub_package_project_id']])
            ->with('success', 'Safeguard entry updated successfully.');
    }

    /**
     * Delete a safeguard entry.
     */
    public function destroy(SafeguardEntry $safeguardEntry)
    {
        $projectId = $safeguardEntry->sub_package_project_id;
        $safeguardEntry->delete();

        return redirect()
            ->route('admin.safeguard_entries.index', ['sub_package_project_id' => $projectId])
            ->with('success', 'Safeguard entry deleted successfully.');
    }

    /**
     * Bulk delete safeguard entries.
     */
public function bulkDelete(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:safeguard_entries,id',
        'sub_package_project_id' => 'required|exists:sub_package_projects,id',
    ]);

    try {
        // ✅ Log first, to confirm request reaches here
        \Log::info('Bulk delete request hit', $request->all());

        // Delete entries
        SafeguardEntry::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected safeguard entries deleted successfully.'
        ]);

    } catch (\Exception $e) {
        \Log::error('Bulk delete failed', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}


    /**
     * Group safeguard entries by main sl_no (and keep different phases distinct).
     */
    private function groupEntries($entries)
    {
        return $entries->groupBy(function ($item) {
            $mainNo = explode('.', $item->sl_no)[0] ?? $item->sl_no;
            return $mainNo . '-' . $item->contraction_phase_id;
        });
    }

    /**
     * Form data for create/edit views.
     */
    private function formData(): array
    {
        return [
            'compliances' => SafeguardCompliance::all(),
            'phases' => ContractionPhase::all(),
            'projects' => SubPackageProject::all(),
        ];
    }

    /**
     * Validate safeguard entry data.
     */
    private function validateEntry(Request $request): array
    {
        return $request->validate([
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'safeguard_compliance_id' => 'required|exists:safeguard_compliances,id',
            'contraction_phase_id' => 'required|exists:contraction_phases,id',
            'sl_no' => 'nullable|string|max:1000',
            'item_description' => 'nullable|string',
            'is_validity' => 'nullable|boolean',
        ]);
    }

    /**
     * Validate safeguard import data.
     */
    private function validateImport(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'safeguard_compliance_id' => 'required|exists:safeguard_compliances,id',
            'contraction_phase_id' => 'required|exists:contraction_phases,id',
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
    }
    public function index2(Request $request)
    {
        // Fetch all sub-projects
        $subProjects = SubPackageProject::select('id', 'name')->get();

        // Selected project ID
        $selectedProjectId = $request->input('sub_package_project_id');

        $subProject = null;
        $entries = collect();

        // Load related data
        $safeguardCompliances = SafeguardCompliance::all();
        $contractionPhases = ContractionPhase::all();

        if ($selectedProjectId) {
            $subProject = SubPackageProject::findOrFail($selectedProjectId);

            // Load only entries for selected project
            $entries = SafeguardEntry::with(['safeguardCompliance', 'contractionPhase'])
                ->where('sub_package_project_id', $selectedProjectId)
                ->get();
        }

        // Determine which sub-projects have entries
        $projectsStatus = $subProjects->mapWithKeys(function ($project) {
            $done = SafeguardEntry::where('sub_package_project_id', $project->id)->exists();
            return [$project->id => $done];
        });

        return view('admin.safeguard_entries.index-1', compact('subProjects', 'entries', 'subProject', 'selectedProjectId', 'projectsStatus', 'safeguardCompliances', 'contractionPhases'));
    }
}
