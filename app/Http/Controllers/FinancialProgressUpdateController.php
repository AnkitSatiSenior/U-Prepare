<?php

namespace App\Http\Controllers;

use App\Models\FinancialProgressUpdate;
use App\Models\SubPackageProject;
use App\Models\EpcEntryData;
use App\Models\AlreadyDefinedWorkProgress;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FinancialProgressUpdateController extends Controller
{
    private const S3_DISK = 's3';

    /**
     * Display a listing of components and EPC entries.
     */
    public function index2(Request $request)
    {
        $subProjects = SubPackageProject::with('packageProject')->get();

        $departmentIds = $subProjects->pluck('packageProject.department_id')->unique()->filter();

        $components = AlreadyDefinedWorkProgress::with('workService')
            ->whereHas('workService', function ($query) use ($departmentIds) {
                $query->whereIn('department_id', $departmentIds);
            })->get();

        $selectedSubProjectId = $request->input('sub_package_project_id') ?: $subProjects->first()?->id;

        $epcEntries = $selectedSubProjectId 
            ? EpcEntryData::where('sub_package_project_id', $selectedSubProjectId)->orderBy('sl_no')->get()
            : collect();

        return view('admin.financial_progress_update.index-2', [
            'subProjects'          => $subProjects,
            'epcEntries'           => $epcEntries,
            'components'           => $components, 
            'selectedSubProjectId' => $selectedSubProjectId,
        ]);
    }

    /**
     * Display financial progress updates with S3 Media paths.
     */
    public function index(Request $request)
    {
        $subProjects = SubPackageProject::select('id', 'name')->get();
        $selectedProjectId = $request->input('sub_package_project_id');

        $subProject = $selectedProjectId ? SubPackageProject::find($selectedProjectId) : null;

        $financialProgress = collect();

        if ($subProject) {
            $financialProgress = FinancialProgressUpdate::where('project_id', $selectedProjectId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($progress) {
                    $mediaIds = is_array($progress->media) ? $progress->media : json_decode($progress->media, true) ?? [];
                    
                    // Fetch paths and convert to S3 URLs
                    $progress->media_paths = MediaFile::whereIn('id', $mediaIds)
                        ->pluck('path')
                        ->map(fn($path) => Storage::disk(self::S3_DISK)->url($path))
                        ->toArray();

                    return $progress;
                });
        }

        return view('admin.financial_progress_update.index', compact('subProjects', 'subProject', 'financialProgress', 'selectedProjectId'));
    }

    public function create(Request $request)
    {
        $subProjectId = $request->required('sub_package_project_id');
        $subProject = SubPackageProject::findOrFail($subProjectId);

        return view('admin.financial_progress_update.create', compact('subProject'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $subProject = SubPackageProject::findOrFail($validated['project_id']);

        // Business Logic: Prevent over-spending contract value
        $totalFinance = FinancialProgressUpdate::where('project_id', $subProject->id)->sum('finance_amount');
        if (($totalFinance + $validated['finance_amount']) > $subProject->contract_value) {
            return back()->withInput()->with([
                'status' => 'error',
                'message' => 'Finance amount exceeds the contract value (₹' . number_format($subProject->contract_value, 2) . ').'
            ]);
        }

        $validated['media'] = $this->handleMediaUploads($request);

        FinancialProgressUpdate::create($validated);

        return redirect()
            ->route('admin.financial-progress-updates.index', ['sub_package_project_id' => $validated['project_id']])
            ->with('status', 'success')
            ->with('message', 'Update created successfully.');
    }

    /**
     * Handle S3 Media Upload Logic
     */
    private function handleMediaUploads(Request $request, array $existingMediaIds = []): array
    {
        $uploadedMediaIds = $existingMediaIds;

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                // Store on S3 with public visibility (or 'private' if needed)
                $path = $file->store('financial_progress', self::S3_DISK);

                $media = MediaFile::create([
                    'path' => $path, // This is now the S3 path
                    'type' => $file->getClientMimeType(),
                    'meta_data' => [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'disk' => self::S3_DISK,
                        'uploaded_at' => now()->toDateTimeString(),
                    ],
                ]);

                $uploadedMediaIds[] = $media->id;
            }
        }

        return array_values(array_unique($uploadedMediaIds));
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'project_id'     => 'required|integer|exists:sub_package_projects,id',
            'finance_amount' => 'required|numeric|min:0',
            'no_of_bills'    => 'required|integer|min:1',
            'bill_serial_no' => 'nullable|string',
            'submit_date'    => 'required|date',
            'media.*'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // Increased to 5MB for S3
        ]);
    }
}