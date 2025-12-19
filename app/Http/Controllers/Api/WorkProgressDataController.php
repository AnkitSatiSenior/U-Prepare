<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkProgressData;
use App\Models\AlreadyDefinedWorkProgress;
use App\Models\SubPackageProject;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkProgressDataController extends Controller
{
    /**
     * Display all projects with their work progress.
     */
    public function index()
    {
        $projects = SubPackageProject::with(['workProgressData.workComponent'])->get();

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get all components and existing entries for a project.
     */
    public function create(Request $request)
    {
        $components = AlreadyDefinedWorkProgress::with('workService')->get();
        $project = null;
        $existingEntries = collect();

        if ($request->has('sub_package_project_id')) {
            $project = SubPackageProject::with('workProgressData')->find($request->sub_package_project_id);

            if ($project) {
                $existingEntries = $project->workProgressData->groupBy('work_component_id')->map(function ($items) {
                    $totalProgress = $items->sum('progress_percentage');
                    $lastEntry = $items->sortByDesc('created_at')->first();

                    return [
                        'total_progress' => min(100, $totalProgress),
                        'last_entry' => $lastEntry,
                    ];
                });
            }
        }

        return response()->json([
            'success' => true,
            'components' => $components,
            'project' => $project,
            'existing_entries' => $existingEntries
        ]);
    }

    /**
     * Store Work Progress data (new + updates) with optional images.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer|exists:sub_package_projects,id',
            'entries' => 'sometimes|array',
            'updates' => 'sometimes|array',
        ]);

        $user = $request->user(); // fetched from AuthTokenMiddleware

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: User not found.'
            ], 401);
        }

        $projectId = $request->project_id;

        // Handle new entries
        if ($request->has('entries')) {
            foreach ($request->entries as $componentId => $entry) {
                $images = $this->uploadImages($request, "entries.$componentId.images");
                $this->saveProgressEntry($projectId, $componentId, $entry, $user->id, $images);
            }
        }

        // Handle updates
        if ($request->has('updates')) {
            foreach ($request->updates as $componentId => $update) {
                $images = $this->uploadImages($request, "updates.$componentId.images");
                $this->saveProgressEntry($projectId, $componentId, $update, $user->id, $images);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Work Progress Data saved successfully.'
        ]);
    }

    /**
     * Show details of a single project along with all work progress entries.
     */
    public function show($id)
    {
        $project = SubPackageProject::with([
            'workProgressData' => function ($q) {
                $q->with('user', 'workComponent')->orderBy('created_at', 'desc');
            },
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'project' => $project
        ]);
    }

    /**
     * Upload images and return array of uploaded info.
     */
    private function uploadImages(Request $request, string $inputKey): ?array
    {
        $uploadedImages = [];

        if ($request->hasFile($inputKey)) {
            foreach ($request->file($inputKey) as $file) {
                if ($file->isValid()) {
                    $path = $file->store('uploads/work_progress', 'public');
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
     * Upload images to the latest progress entry for a component.
     */
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

        $user = $request->user();

        $progress = WorkProgressData::where('project_id', $request->project_id)
            ->where('work_component_id', $request->work_component_id)
            ->latest('created_at')
            ->first();

        $mediaIds = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if (!$file->isValid()) continue;

                $path = $file->store('uploads/media_files', 'public');

                $media = MediaFile::create([
                    'path' => 'storage/' . $path,
                    'type' => $file->getClientMimeType(),
                    'meta_data' => [
                        'original_name' => $file->getClientOriginalName(),
                        'description' => $request->description,
                        'uploaded_by' => $user->name,
                        'uploaded_at' => now()->toDateTimeString(),
                    ],
                    'lat' => $request->lat,
                    'long' => $request->long,
                ]);

                $mediaIds[] = $media->id;
            }
        }

        if ($progress) {
            $existingIds = $progress->images ?? [];
            $merged = array_values(array_unique(array_merge($existingIds, $mediaIds)));

            $progress->update([
                'images' => $merged,
                'remarks' => $request->description ?? $progress->remarks,
            ]);

            $message = 'Existing Work Progress updated successfully with new images.';
        } else {
            WorkProgressData::create([
                'project_id' => $request->project_id,
                'work_component_id' => $request->work_component_id,
                'qty_length' => null,
                'current_stage' => null,
                'progress_percentage' => 0,
                'remarks' => $request->description ?? 'Image upload entry created automatically.',
                'date_of_entry' => now()->toDateString(),
                'user_id' => $user->id,
                'images' => $mediaIds,
            ]);

            $message = 'New Work Progress entry created successfully with uploaded images.';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Save a progress entry safely.
     */
    private function saveProgressEntry(int $projectId, int $componentId, array $data, int $userId, ?array $images = null): void
    {
        $existingTotal = WorkProgressData::where('project_id', $projectId)
            ->where('work_component_id', $componentId)
            ->sum('progress_percentage');

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
                'images' => $images,
            ]);
        }
    }

    /**
     * Delete a progress entry along with associated media files.
     */
    public function destroy($id)
    {
        $progress = WorkProgressData::findOrFail($id);

        if (!empty($progress->images)) {
            $mediaFiles = MediaFile::whereIn('id', $progress->images)->get();

            foreach ($mediaFiles as $media) {
                if (Storage::disk('public')->exists(str_replace('storage/', '', $media->path))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $media->path));
                }
                $media->delete();
            }
        }

        $progress->delete();

        return response()->json([
            'success' => true,
            'message' => 'Work Progress entry deleted successfully.'
        ]);
    }
}
