<?php

namespace App\Http\Controllers;

use App\Models\FinancialProgressUpdate;
use App\Models\SubPackageProject;
use App\Models\EpcEntryData;
use App\Models\AlreadyDefinedWorkProgress;
use App\Models\MediaFile;
use Illuminate\Http\Request;

class FinancialProgressUpdateController extends Controller
{
public function index2(Request $request)
{
    // Fetch all sub projects
    $subProjects = SubPackageProject::all();
$components = AlreadyDefinedWorkProgress::with('workService')->get();

    // default empty collection
    $epcEntries = collect();
    $selectedSubProjectId = null;

    // choose requested id or first project id
    if ($request->filled('sub_package_project_id')) {
        $selectedSubProjectId = $request->sub_package_project_id;
    } elseif ($subProjects->isNotEmpty()) {
        $selectedSubProjectId = $subProjects->first()->id;
    }

    // only fetch when we have a valid id
    if ($selectedSubProjectId) {
        $epcEntries = EpcEntryData::where('sub_package_project_id', $selectedSubProjectId)
            ->orderBy('sl_no')
            ->get();
    }

    return view('admin.financial_progress_update.index-2', [
        'subProjects' => $subProjects,
        'epcEntries' => $epcEntries,     
        'components' =>$components,          // <--- ensure name matches blade
        'selectedSubProjectId' => $selectedSubProjectId,
    ]);
}


    public function index(Request $request)
    {
        $subProjects = SubPackageProject::select('id', 'name')->get();
        $selectedProjectId = $request->input('sub_package_project_id');

        $subProject = $selectedProjectId ? SubPackageProject::find($selectedProjectId) : null;

        $financialProgress = $subProject
            ? FinancialProgressUpdate::where('project_id', $selectedProjectId)
                ->orderBy('created_at', 'desc') // optional: latest first
                ->get()
                ->map(function ($progress) {
                    // Decode media IDs
                    $mediaIds = is_array($progress->media) ? $progress->media : json_decode($progress->media, true) ?? [];

                    // Get file paths
                    $paths = MediaFile::whereIn('id', $mediaIds)->pluck('path')->toArray();
                    $progress->media_paths = $paths;

                    return $progress;
                })
            : collect();

        return view('admin.financial_progress_update.index', compact('subProjects', 'subProject', 'financialProgress', 'selectedProjectId'));
    }

    public function create(Request $request)
    {
        $subProjectId = $request->input('sub_package_project_id');
        $subProject = SubPackageProject::findOrFail($subProjectId);

        return view('admin.financial_progress_update.create', compact('subProject'));
    }

    public function edit($id)
    {
        $update = FinancialProgressUpdate::findOrFail($id);
        $subProject = SubPackageProject::findOrFail($update->project_id);

        // Fetch media files for display
        $mediaFiles = MediaFile::whereIn('id', $update->media ?? [])->get();

        return view('admin.financial_progress_update.edit', compact('update', 'subProject', 'mediaFiles'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $subProject = SubPackageProject::findOrFail($validated['project_id']);

        // Calculate already stored progress
        $totalFinance = FinancialProgressUpdate::where('project_id', $subProject->id)->sum('finance_amount');

        // New total after adding current update
        $newTotal = $totalFinance + $validated['finance_amount'];

        if ($newTotal > $subProject->contract_value) {
            return back()
                ->withInput()
                ->with('status', 'error')
                ->with('message', 'Finance amount exceeds the contract value (₹' . number_format($subProject->contract_value, 2) . ').');
        }

        $validated['media'] = $this->handleMediaUploads($request);

        FinancialProgressUpdate::create($validated);

        return redirect()
            ->route('admin.financial-progress-updates.index', [
                'sub_package_project_id' => $validated['project_id'],
            ])
            ->with('status', 'success')
            ->with('message', 'Financial progress update created successfully.');
    }

    public function update(Request $request, $id)
    {
        $update = FinancialProgressUpdate::findOrFail($id);
        $validated = $this->validateRequest($request);

        $subProject = SubPackageProject::findOrFail($validated['project_id']);

        // Exclude current record then add new amount
        $totalFinance = FinancialProgressUpdate::where('project_id', $subProject->id)->where('id', '!=', $update->id)->sum('finance_amount');

        $newTotal = $totalFinance + $validated['finance_amount'];

        if ($newTotal > $subProject->contract_value) {
            return back()
                ->withInput()
                ->with('status', 'error')
                ->with('message', 'Finance amount exceeds the contract value (₹' . number_format($subProject->contract_value, 2) . ').');
        }

        $validated['media'] = $this->handleMediaUploads($request, $update->media ?? []);

        $update->update($validated);

        return redirect()
            ->route('admin.financial-progress-updates.index', [
                'sub_package_project_id' => $validated['project_id'],
            ])
            ->with('status', 'success')
            ->with('message', 'Financial progress update updated successfully.');
    }

    public function destroy($id)
    {
        $update = FinancialProgressUpdate::findOrFail($id);
        $update->delete();

        return back()->with('status', 'success')->with('message', 'Financial progress update deleted successfully.');
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'media.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $mediaIds = $this->handleMediaUploads($request);

        return response()->json([
            'status' => 'success',
            'media_ids' => $mediaIds,
            'files' => MediaFile::whereIn('id', $mediaIds)->get(),
        ]);
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'project_id' => 'required|integer|exists:sub_package_projects,id',
            'finance_amount' => 'required|numeric|min:0',
            'no_of_bills' => 'required|integer|min:1',
            'bill_serial_no' => 'nullable|string',
            'submit_date' => 'required|date',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
    }

    private function handleMediaUploads(Request $request, array $existingMediaIds = []): array
    {
        $uploadedMediaIds = $existingMediaIds;

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('financial_progress', 'public');

                $media = MediaFile::create([
                    'path' => $path,
                    'type' => $file->getClientMimeType(),
                    'meta_data' => [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime' => $file->getClientMimeType(),
                        'uploaded_at' => now()->toDateTimeString(),
                        'uploaded_by' => auth()->id(),
                    ],
                ]);

                $uploadedMediaIds[] = $media->id;
            }
        }

        return array_values(array_unique($uploadedMediaIds));
    }
}
