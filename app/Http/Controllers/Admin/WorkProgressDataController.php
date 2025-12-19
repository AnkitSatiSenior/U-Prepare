<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkProgressData;
use App\Models\AlreadyDefinedWorkProgress;
use App\Models\SubPackageProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\MediaFile;

class WorkProgressDataController extends Controller
{
    /**
     * Display all projects with their work progress.
     */
    public function index(Request $request)
    {
        $projects = SubPackageProject::with(['workProgressData.workComponent'])->get();
        return view('admin.work_progress_data.index', compact('projects'));
    }

    /**
     * Show the create form for work progress entries.
     */
    public function create(Request $request)
    {
        $components = AlreadyDefinedWorkProgress::with('workService')->get();

        $project = null;
        $existingEntries = collect();

        if ($request->sub_package_project_id) {
            $project = SubPackageProject::with('workProgressData')->find($request->sub_package_project_id);

            // Group entries by component and calculate totals
            $existingEntries = $project->workProgressData->groupBy('work_component_id')->map(function ($items) {
                $totalProgress = $items->sum('progress_percentage');
                $lastEntry = $items->sortByDesc('created_at')->first();

                return (object) [
                    'total_progress' => min(100, $totalProgress), // cap at 100%
                    'last_entry' => $lastEntry,
                ];
            });
        }

        return view('admin.work_progress_data.create', [
            'components' => $components,
            'project' => $project,
            'existingEntries' => $existingEntries,
        ]);
    }

    /**
     * Store Work Progress data (new + updates) with images.
     */
    public function store(Request $request)
    {
        $projectId = $request->project_id;
        $userId = auth()->id();

        // ✅ Handle "new" entries
        if ($request->has('entries')) {
            foreach ($request->entries as $componentId => $entry) {
                $images = $this->uploadImages($request, "entries.$componentId.images");
                $this->saveProgressEntry($projectId, $componentId, $entry, $userId, $images);
            }
        }

        // ✅ Handle "update" entries
        if ($request->has('updates')) {
            foreach ($request->updates as $componentId => $update) {
                $images = $this->uploadImages($request, "updates.$componentId.images");
                $this->saveProgressEntry($projectId, $componentId, $update, $userId, $images);
            }
        }

        return redirect()->back()->with('success', 'Work Progress Data saved successfully.');
    }

    /**
     * Display details of a single project with all progress entries.
     */
    public function show($id)
    {
        $project = SubPackageProject::with([
            'workProgressData' => function ($q) {
                $q->with('user', 'workComponent')->orderBy('created_at', 'desc');
            },
        ])->findOrFail($id);

        return view('admin.work_progress_data.show', compact('project'));
    }

    /**
     * Upload images and return array of paths (stored as JSON later).
     */
    private function uploadImages(Request $request, string $inputKey): ?array
    {
        $uploadedImages = [];

        if ($request->hasFile($inputKey)) {
            foreach ($request->file($inputKey) as $file) {
                if ($file->isValid()) {
                    $path = $file->store('uploads/work_progress', 'public'); // stored in storage/app/public/uploads/work_progress
                    $uploadedImages[] = [
                        'url' => asset('storage/' . $path),
                        'name' => $file->getClientOriginalName(),
                        'uuid' => (string) Str::uuid(),
                    ];
                }
            }
        }

        return !empty($uploadedImages) ? $uploadedImages : null;
    }
    /**
     * Upload new images and attach them to the latest WorkProgressData record
     * for a given sub_package_project_id.
     */
    public function showProjectImages($projectId)
    {
        $project = SubPackageProject::with(['workProgressData.workComponent', 'workProgressData.user'])->findOrFail($projectId);

        $allMedia = [];

        foreach ($project->workProgressData as $progress) {
            if (!empty($progress->images)) {
                $mediaFiles = MediaFile::whereIn('id', $progress->images)->get();

                foreach ($mediaFiles as $media) {
                    $allMedia[] = [
                        'component_name' => $progress->workComponent->work_component ?? 'N/A',
                        'component_details' => $progress->workComponent->type_details ?? 'N/A',
                        'description' => $media->meta_data['description'] ?? 'No description available',
                        'path' => asset($media->path),
                        'uploaded_by' => $media->meta_data['uploaded_by'] ?? 'Unknown',
                        'uploaded_at' => \Carbon\Carbon::parse($media->meta_data['uploaded_at'] ?? now())->format('d M Y, h:i A'),
                        'remarks' => $progress->remarks ?? 'No remarks',
                        'work_component_id' => $progress->work_component_id,
                    ];
                }
            }
        }
        $groupedMedia = collect($allMedia)->groupBy(function ($item) {
            return $item['component_name'] . '||' . $item['component_details'];
        });

        return view('admin.work_progress_data.gallery', compact('project', 'allMedia', 'groupedMedia'));
    }

    public function uploadImagesToLastProgress(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer|exists:sub_package_projects,id',
            'work_component_id' => 'required|integer|exists:already_defined_work_progress,id',
            'description' => 'nullable|string|max:500',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:4096',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
        ]);

        // ✅ Get the latest progress record for this project & component
        $progress = WorkProgressData::where('project_id', $request->project_id)->where('work_component_id', $request->work_component_id)->latest('created_at')->first();

        $mediaIds = [];

        // ✅ Upload files and create media records
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if (!$file->isValid()) {
                    continue;
                }

                $path = $file->store('uploads/media_files', 'public');

                // ✅ Create new MediaFile entry
                $media = MediaFile::create([
                    'path' => 'storage/' . $path,
                    'type' => $file->getClientMimeType(),
                    'meta_data' => [
                        'original_name' => $file->getClientOriginalName(),
                        'description' => $request->description,
                        'uploaded_by' => auth()->user()?->name,
                        'uploaded_at' => now()->toDateTimeString(),
                    ],
                    'lat' => $request->lat,
                    'long' => $request->long,
                ]);

                $mediaIds[] = $media->id;
            }
        }

        // ✅ Case 1: If progress exists → append image IDs
        if ($progress) {
            $existingIds = $progress->images ?? [];
            $merged = array_values(array_unique(array_merge($existingIds, $mediaIds)));

            $progress->update([
                'images' => $merged,
                'remarks' => $request->description ?? $progress->remarks,
            ]);

            $message = '✅ Existing Work Progress updated successfully with new images.';
        }
        // ✅ Case 2: No existing entry → create a new one
        else {
            WorkProgressData::create([
                'project_id' => $request->project_id,
                'work_component_id' => $request->work_component_id,
                'qty_length' => null,
                'current_stage' => null,
                'progress_percentage' => 0,
                'remarks' => $request->description ?? 'Image upload entry created automatically.',
                'date_of_entry' => now()->toDateString(),
                'user_id' => auth()->id(),
                'images' => $mediaIds, // store [12,23,...]
            ]);

            $message = '✅ New Work Progress entry created successfully with uploaded images.';
        }

        return redirect()->back()->with('success', $message);
    }
    /**
     * Save a progress entry safely (with optional images).
     */
    private function saveProgressEntry(int $projectId, int $componentId, array $data, int $userId, ?array $images = null): void
    {
        $existingTotal = WorkProgressData::where('project_id', $projectId)->where('work_component_id', $componentId)->sum('progress_percentage');

        $newProgress = min(100 - $existingTotal, $data['progress_percentage'] ?? 0);

        if ($newProgress > 0) {
            WorkProgressData::create([
                'project_id' => $projectId,
                'work_component_id' => $componentId,
                'qty_length' => $data['qty_length'] ?? null,
                'current_stage' => $data['current_stage'] ?? null,
                'progress_percentage' => $newProgress,
                'remarks' => $data['remarks'] ?? null,
                'date_of_entry' => $data['date_of_entry'] ?? now()->toDateString(),
                'user_id' => $userId,
                'images' => $images, // ✅ JSON array saved automatically
            ]);
        }
    }
    /**
     * Delete a Work Progress entry along with associated media files.
     */
    public function destroy(Request $request, $id)
    {
        $progress = WorkProgressData::findOrFail($id);

        // ✅ Delete associated media files if any
        if (!empty($progress->images)) {
            $mediaFiles = MediaFile::whereIn('id', $progress->images)->get();

            foreach ($mediaFiles as $media) {
                // Delete the file from storage
                if (Storage::disk('public')->exists(str_replace('storage/', '', $media->path))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $media->path));
                }

                // Delete the MediaFile record
                $media->delete();
            }
        }

        // Delete the work progress entry itself
        $progress->delete();

        return redirect()->back()->with('success', '✅ Work Progress entry deleted successfully.');
    }
}
