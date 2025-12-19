<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProcurementWorkProgram;
use App\Models\PackageProject;
use App\Models\ProcurementDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProcurementWorkProgramController extends Controller
{
    public function index()
    {
        $packageProjects = PackageProject::withWorkProgramData()->latest()->get();

        return view('admin.procurement_work_programs.index', compact('packageProjects'));
    }

    public function create(Request $request)
    {
        // Get all projects for dropdown (basic info)
        $packageProjects = PackageProject::basicInfo()->get();

        // Selected project (with procurementDetail + workPrograms)
        $selectedPackageProject = null;

        if ($request->package_project_id) {
            $selectedPackageProject = PackageProject::withWorkProgramData()->find($request->package_project_id);
        }

        return view('admin.procurement_work_programs.create', [
            'packageProjects' => $packageProjects, // All projects for dropdown
            'selectedPackageProject' => $selectedPackageProject, // Full details of selected project
            'procurementDetails' => $selectedPackageProject?->procurementDetail, // Single related detail
            'procurementDetailsForm' => $selectedPackageProject?->procurementDetail, // Single related detail
            'selectedPackageProjectId' => $request->package_project_id,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->has('procurement_bid_document')) {
            $procurementBidDocs = array_filter($request->file('procurement_bid_document'), fn($file) => $file !== null);
            $request->merge(['procurement_bid_document' => $procurementBidDocs]);
        }

        if ($request->has('pre_bid_minutes_document')) {
            $preBidMinutesDocs = array_filter($request->file('pre_bid_minutes_document'), fn($file) => $file !== null);
            $request->merge(['pre_bid_minutes_document' => $preBidMinutesDocs]);
        }

        $data = $request->validate([
            'package_project_id' => 'required|array|min:1',
            'package_project_id.*' => 'required|exists:package_projects,id',

            'procurement_details_id' => 'required|array|min:1',
            'procurement_details_id.*' => 'required|exists:procurement_details,id',

            'name_work_program' => 'required|array|min:1',
            'name_work_program.*' => 'required|string|max:255',

            'weightage' => 'required|array|min:1',
            'weightage.*' => 'required|numeric|min:0|max:100',

            'days' => 'nullable|array',
            'days.*' => 'nullable|integer|min:0',

            'start_date' => 'nullable|array',
            'start_date.*' => 'nullable|date',

            'planned_date' => 'nullable|array',
            'planned_date.*' => 'nullable|date',

            'procurement_bid_document' => 'nullable|array',
            'procurement_bid_document.*' => 'sometimes|file|mimes:pdf,doc,docx,jpg,png',

            'pre_bid_minutes_document' => 'nullable|array',
            'pre_bid_minutes_document.*' => 'sometimes|file|mimes:pdf,doc,docx,jpg,png',
        ]);

        $procurementId = $data['procurement_details_id'][0];
        $currentTotal = ProcurementWorkProgram::where('procurement_details_id', $procurementId)->sum('weightage');
        $newTotal = $currentTotal + array_sum($data['weightage']);

        if ($currentTotal === 100) {
            return back()
                ->withInput()
                ->withErrors([
                    'weightage_total' => 'This procurement already has 100% total weightage. Cannot add more work programs.',
                ]);
        }
        if ($newTotal > 100) {
            return back()
                ->withInput()
                ->withErrors([
                    'weightage_total' => "Adding these work programs exceeds 100% total weightage (Current: {$currentTotal}%, Attempted: {$newTotal}%).",
                ]);
        }
        if ($newTotal < 100) {
            return back()
                ->withInput()
                ->withErrors([
                    'weightage_total' => "Total weightage after adding will be {$newTotal}%. Must reach exactly 100%.",
                ]);
        }

        $commonData = [
            'package_project_id' => $data['package_project_id'][0],
            'procurement_details_id' => $procurementId,
        ];

        $procurementBidDocPath = null;
        if (!empty($data['procurement_bid_document']) && $request->hasFile('procurement_bid_document.0')) {
            $procurementBidDocPath = $request->file('procurement_bid_document.0')->store('procurement_docs', 'public');
        }

        $preBidMinutesDocPath = null;
        if (!empty($data['pre_bid_minutes_document']) && $request->hasFile('pre_bid_minutes_document.0')) {
            $preBidMinutesDocPath = $request->file('pre_bid_minutes_document.0')->store('procurement_docs', 'public');
        }

        foreach ($data['name_work_program'] as $i => $name) {
            $rowData = [
                'name_work_program' => $name,
                'weightage' => $data['weightage'][$i],
                'days' => $data['days'][$i] ?? null,
                'start_date' => $data['start_date'][$i] ?? null,
                'planned_date' => $data['planned_date'][$i] ?? null,
                'procurement_bid_document' => $procurementBidDocPath,
                'pre_bid_minutes_document' => $preBidMinutesDocPath,
            ];

            ProcurementWorkProgram::create(array_merge($commonData, $rowData));
        }

        return redirect()->route('admin.procurement-work-programs.index')->with('success', 'Work programs created successfully.');
    }

    public function storeSingle(Request $request)
    {
        $validated = $request->validate([
            'package_project_id' => 'required|exists:package_projects,id',
            'procurement_details_id' => 'required|exists:procurement_details,id',
            'name_work_program' => 'required|string|max:255',
            'weightage' => 'required|numeric|min:0|max:100',
            'days' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'planned_date' => 'nullable|date',
            'procurement_bid_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png',
            'pre_bid_minutes_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png',
        ]);

        if ($request->hasFile('procurement_bid_document')) {
            $validated['procurement_bid_document'] = $request->file('procurement_bid_document')->store('procurement_docs', 'public');
        }

        if ($request->hasFile('pre_bid_minutes_document')) {
            $validated['pre_bid_minutes_document'] = $request->file('pre_bid_minutes_document')->store('procurement_docs', 'public');
        }

        $totalWeightage = ProcurementWorkProgram::where('package_project_id', $validated['package_project_id'])->where('procurement_details_id', $validated['procurement_details_id'])->sum('weightage') + $validated['weightage'];

        if ($totalWeightage > 100) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Adding this work program exceeds 100% total weightage.',
                ],
                422,
            );
        }

        $program = ProcurementWorkProgram::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Work program added successfully.',
            'data' => $program,
        ]);
    }
    public function uploadDocumentsAndUpdate(Request $request, $package_project_id, $procurement_details_id)
    {
        $request->validate([
            'procurement_bid_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png',
            'pre_bid_minutes_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png',
        ]);

        $workProgram = ProcurementWorkProgram::where('package_project_id', $package_project_id)->where('procurement_details_id', $procurement_details_id)->first();

        if (!$workProgram) {
            return back()->withErrors(['error' => 'Work program not found for the given IDs.']);
        }

        $updateData = [];

        if ($request->hasFile('procurement_bid_document')) {
            $path = $request->file('procurement_bid_document')->store('procurement_docs', 'public');
            $updateData['procurement_bid_document'] = $path;
        }

        if ($request->hasFile('pre_bid_minutes_document')) {
            $path = $request->file('pre_bid_minutes_document')->store('procurement_docs', 'public');
            $updateData['pre_bid_minutes_document'] = $path;
        }

        if (!empty($updateData)) {
            $workProgram->update($updateData);
        }

        return redirect()
            ->route('admin.procurement-work-programs.edit-by-package', [$package_project_id, $procurement_details_id])
            ->with('success', 'Documents uploaded and work program updated successfully.');
    }

    public function edit($id)
    {
        $workProgram = ProcurementWorkProgram::with(['packageProject', 'procurementDetail'])->findOrFail($id);

        $relatedWorkPrograms = ProcurementWorkProgram::where('package_project_id', $workProgram->package_project_id)->where('procurement_details_id', $workProgram->procurement_details_id)->get();

        return view('admin.procurement_work_programs.edit', [
            'workProgram' => $workProgram,
            'workPrograms' => $relatedWorkPrograms,
            'packageProjects' => PackageProject::all(),
            'procurementDetails' => ProcurementDetail::all(),
        ]);
    }

    public function editByPackage($package_project_id, $procurement_details_id)
    {
        $workPrograms = ProcurementWorkProgram::where('package_project_id', $package_project_id)->where('procurement_details_id', $procurement_details_id)->get();

        return view('admin.procurement_work_programs.edit', [
            'procurementWorkProgram' => $workPrograms->first(),
            'workPrograms' => $workPrograms,
            'packageProjects' => PackageProject::all(['id', 'package_name']),
            'procurementDetails' => ProcurementDetail::all(['id', 'method_of_procurement']),
        ]);
    }

    public function updateSingle(Request $request, $id)
    {
        $validated = $request->validate([
            'name_work_program' => 'required|string|max:255',
            'weightage' => 'required|numeric|min:0|max:100',
            'days' => 'nullable|integer|min:0',
            'planned_date' => 'nullable|date',
        ]);

        $program = ProcurementWorkProgram::findOrFail($id);
        $program->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Work program updated successfully.',
            'data' => $program,
        ]);
    }

    public function update(Request $request, ProcurementWorkProgram $procurementWorkProgram)
    {
        $validated = $request->validate([
            'package_project_id' => 'required|exists:package_projects,id',
            'procurement_details_id' => 'required|exists:procurement_details,id',
            'name_work_program' => 'required|string|max:255',
            'weightage' => 'required|numeric|min:0|max:100',
            'days' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'planned_date' => 'nullable|date',
            'procurement_bid_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png',
            'pre_bid_minutes_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png',
        ]);

        $existingWeightages = ProcurementWorkProgram::where('package_project_id', $validated['package_project_id'])->where('procurement_details_id', $validated['procurement_details_id'])->pluck('weightage', 'id')->toArray();

        $existingWeightages[$procurementWorkProgram->id] = $validated['weightage'];

        if (array_sum($existingWeightages) !== 100) {
            return back()
                ->withInput()
                ->withErrors([
                    'weightage_total' => 'Total weightage for all programs must be exactly 100%.',
                ]);
        }

        if ($request->hasFile('procurement_bid_document')) {
            if ($procurementWorkProgram->procurement_bid_document && Storage::disk('public')->exists($procurementWorkProgram->procurement_bid_document)) {
                Storage::disk('public')->delete($procurementWorkProgram->procurement_bid_document);
            }
            $validated['procurement_bid_document'] = $request->file('procurement_bid_document')->store('procurement_docs', 'public');
        }

        if ($request->hasFile('pre_bid_minutes_document')) {
            if ($procurementWorkProgram->pre_bid_minutes_document && Storage::disk('public')->exists($procurementWorkProgram->pre_bid_minutes_document)) {
                Storage::disk('public')->delete($procurementWorkProgram->pre_bid_minutes_document);
            }
            $validated['pre_bid_minutes_document'] = $request->file('pre_bid_minutes_document')->store('procurement_docs', 'public');
        }

        $procurementWorkProgram->update($validated);

        return redirect()->route('admin.procurement-work-programs.index')->with('success', 'Work program updated successfully.');
    }

    public function destroy(ProcurementWorkProgram $procurementWorkProgram)
    {
        $procurementWorkProgram->delete();

        return redirect()->route('admin.procurement-work-programs.index')->with('success', 'Work program deleted successfully.');
    }

    public function show($package_project_id, $procurement_details_id)
    {
        $workPrograms = ProcurementWorkProgram::with(['packageProject', 'procurementDetail'])
            ->where('package_project_id', $package_project_id)
            ->where('procurement_details_id', $procurement_details_id)
            ->get();

        $workProgram = $workPrograms->first(); // may be null if none exist

        return view('admin.procurement_work_programs.show', [
            'workProgram' => $workProgram,
            'workPrograms' => $workPrograms,
            'packageProjects' => PackageProject::all(['id', 'package_name']),
            'procurementDetails' => ProcurementDetail::all(['id', 'method_of_procurement']),
            'packageProjectId' => $package_project_id,
            'procurementDetailsId' => $procurement_details_id,
        ]);
    }
}
