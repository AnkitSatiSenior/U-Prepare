<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialProgressUpdate;
use App\Models\SubPackageProject;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinancialProgressUpdateApiController extends Controller
{
    /**
     * GET: List all financial progress updates for a sub-project
     */
    public function index(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|integer|exists:sub_package_projects,id',
        ]);

        $subProjectId = $request->input('sub_package_project_id');

        // 1. Fetch updates
        $updates = FinancialProgressUpdate::where('project_id', $subProjectId)
            ->orderByDesc('created_at')
            ->get();

        // 2. ARCHITECTURE FIX: Prevent N+1 query by collecting all unique Media IDs first
        $allMediaIds = $updates->flatMap(function ($update) {
            return is_string($update->media) ? json_decode($update->media, true) : ($update->media ?? []);
        })->filter()->unique()->toArray();

        // 3. Fetch all related media files in ONE query and key by ID for fast lookup
        $mediaFiles = empty($allMediaIds)
            ? collect()
            : MediaFile::whereIn('id', $allMediaIds)->get()->keyBy('id');

        // 4. Map the data efficiently in memory
        $formattedUpdates = $updates->map(function ($update) use ($mediaFiles) {
            $mediaIds = is_string($update->media) ? json_decode($update->media, true) : ($update->media ?? []);
            
            $update->media_files = collect($mediaIds)->map(function ($id) use ($mediaFiles) {
                if ($mediaFiles->has($id)) {
                    $file = $mediaFiles->get($id);
                    return [
                        'id' => $file->id,
                        // ✅ S3 FIX: Utilize the appended 'url' from the HasS3Image trait
                        'url' => $file->url, 
                        'meta_data' => $file->meta_data,
                    ];
                }
                return null;
            })->filter()->values(); // Remove nulls and reset array keys

            return $update;
        });

        return response()->json([
            'status' => true,
            'data' => $formattedUpdates,
        ]);
    }

    /**
     * POST: Store new financial progress update
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $subProject = SubPackageProject::findOrFail($validated['project_id']);

        // Check total finance does not exceed contract value
        $totalFinance = FinancialProgressUpdate::where('project_id', $subProject->id)->sum('finance_amount');
        if (($totalFinance + $validated['finance_amount']) > $subProject->contract_value) {
            return response()->json([
                'status' => false,
                'message' => 'Finance amount exceeds contract value (₹' . number_format($subProject->contract_value, 2) . ').',
            ], 422);
        }

        $validated['media'] = $this->handleMedia($request);

        $update = FinancialProgressUpdate::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Financial progress update created successfully.',
            'data' => $update,
        ]);
    }

    /**
     * PUT/PATCH: Update an existing financial progress update
     */
    public function update(Request $request, $id)
    {
        $update = FinancialProgressUpdate::findOrFail($id);
        $validated = $this->validateRequest($request);

        $subProject = SubPackageProject::findOrFail($validated['project_id']);

        $totalFinance = FinancialProgressUpdate::where('project_id', $subProject->id)
            ->where('id', '!=', $update->id)
            ->sum('finance_amount');

        if (($totalFinance + $validated['finance_amount']) > $subProject->contract_value) {
            return response()->json([
                'status' => false,
                'message' => 'Finance amount exceeds contract value (₹' . number_format($subProject->contract_value, 2) . ').',
            ], 422);
        }

        $validated['media'] = $this->handleMedia($request, $update->media ?? []);

        $update->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Financial progress update updated successfully.',
            'data' => $update,
        ]);
    }

    /**
     * DELETE: Delete a financial progress update
     */
    public function destroy($id)
    {
        $update = FinancialProgressUpdate::findOrFail($id);

        // ✅ S3 FIX: Clean up orphaned S3 files to prevent storage bloat
        $mediaIds = is_string($update->media) ? json_decode($update->media, true) : ($update->media ?? []);
        
        if (!empty($mediaIds)) {
            $mediaFiles = MediaFile::whereIn('id', $mediaIds)->get();
            foreach ($mediaFiles as $media) {
                if (Storage::disk('s3')->exists($media->path)) {
                    Storage::disk('s3')->delete($media->path);
                }
                $media->delete();
            }
        }

        $update->delete();

        return response()->json([
            'status' => true,
            'message' => 'Financial progress update deleted successfully.',
        ]);
    }

    /**
     * PRIVATE HELPERS
     */
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

    private function handleMedia(Request $request, array $existingMediaIds = []): array
    {
        $uploadedMediaIds = $existingMediaIds;

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if (!$file->isValid()) continue;

                // ✅ S3 FIX: Push directly to S3 disk
                $path = $file->store('uploads/financial_progress', 's3');

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