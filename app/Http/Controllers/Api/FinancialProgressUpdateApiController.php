<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialProgressUpdate;
use App\Models\SubPackageProject;
use App\Models\MediaFile;
use Illuminate\Http\Request;

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

        $updates = FinancialProgressUpdate::where('project_id', $subProjectId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($update) {
                $mediaIds = is_array($update->media) ? $update->media : json_decode($update->media, true) ?? [];
                $update->media_files = MediaFile::whereIn('id', $mediaIds)
                    ->get()
                    ->map(fn($file) => [
                        'id' => $file->id,
                        'url' => asset('storage/' . $file->path),
                        'meta_data' => $file->meta_data,
                    ]);
                return $update;
            });

        return response()->json([
            'status' => true,
            'data' => $updates,
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
