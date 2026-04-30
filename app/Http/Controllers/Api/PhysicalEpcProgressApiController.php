<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhysicalEpcProgress;
use App\Models\EpcEntryData;
use App\Models\MediaFile;
use App\Models\SubPackageProject;
use Illuminate\Http\Request;

class PhysicalEpcProgressApiController extends Controller
{
    /**
     * GET: Fetch EPC progress data with remarks & images
     * ARCHITECTURE FIX: Eliminated N+1 queries using in-memory aggregate mapping.
     */
    public function indexWithEntries(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|integer'
        ]);

        $subProjectId = $request->sub_package_project_id;

        // 1. Fetch all EPC entries
        $epcEntries = EpcEntryData::where('sub_package_project_id', $subProjectId)
            ->orderBy('sl_no')
            ->get();

        $epcEntryIds = $epcEntries->pluck('id')->toArray();

        // 2. Fetch ALL related progress in ONE query
        $allProgress = PhysicalEpcProgress::whereIn('epcentry_data_id', $epcEntryIds)->get();

        // 3. Process aggregates and latest entries in memory
        $progressSums = [];
        $latestProgressMap = [];
        $allMediaIds = [];

        foreach ($allProgress as $progress) {
            // Aggregate totals
            $progressSums[$progress->epcentry_data_id] = ($progressSums[$progress->epcentry_data_id] ?? 0) + $progress->percent;

            // Track latest entry
            if (!isset($latestProgressMap[$progress->epcentry_data_id]) || $progress->created_at > $latestProgressMap[$progress->epcentry_data_id]->created_at) {
                $latestProgressMap[$progress->epcentry_data_id] = $progress;
            }
        }

        // 4. Extract all required media IDs for the "latest" entries
        foreach ($latestProgressMap as $latest) {
            if (!empty($latest->images)) {
                $allMediaIds = array_merge($allMediaIds, (array) $latest->images);
            }
        }

        // 5. Fetch all required Media in ONE query
        $allMediaIds = array_unique($allMediaIds);
        $mediaFiles = empty($allMediaIds)
            ? collect()
            : MediaFile::whereIn('id', $allMediaIds)->get()->keyBy('id');

        // 6. Map the final response without hitting the database in the loop
        $formattedEntries = $epcEntries->map(function ($entry) use ($progressSums, $latestProgressMap, $mediaFiles) {
            $submittedPercent = $progressSums[$entry->id] ?? 0;
            $entry->remaining_percent = max(0, $entry->percent - $submittedPercent);

            $lastProgress = $latestProgressMap[$entry->id] ?? null;
            $entry->latest_remarks = $lastProgress->items ?? null;

            $mediaIds = $lastProgress ? (array) ($lastProgress->images ?? []) : [];

            $entry->latest_images = collect($mediaIds)->map(function ($id) use ($mediaFiles) {
                // ✅ S3 FIX: Returns the resolved S3 URL via HasS3Image trait
                return $mediaFiles->has($id) ? $mediaFiles->get($id)->url : null;
            })->filter()->values();

            return $entry;
        });

        return response()->json([
            'status' => true,
            'data' => $formattedEntries
        ]);
    }

    /**
     * GET: Fetch all progress entries for a sub-project
     * ARCHITECTURE FIX: Eliminated N+1 queries using batched media retrieval.
     */
    public function index(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|integer'
        ]);

        // 1. Fetch progress entries
        $progressEntries = PhysicalEpcProgress::with(['epcEntryData'])
            ->whereHas('epcEntryData', function ($q) use ($request) {
                $q->where('sub_package_project_id', $request->sub_package_project_id);
            })
            ->latest()
            ->get();

        // 2. Extract unique media IDs
        $allMediaIds = $progressEntries->pluck('images')->flatten()->filter()->unique()->toArray();

        // 3. Fetch media in ONE query
        $mediaFiles = empty($allMediaIds)
            ? collect()
            : MediaFile::whereIn('id', $allMediaIds)->get()->keyBy('id');

        // 4. Map in memory
        $formattedEntries = $progressEntries->map(function ($entry) use ($mediaFiles) {
            $mediaIds = (array) ($entry->images ?? []);

            $entry->image_urls = collect($mediaIds)->map(function ($id) use ($mediaFiles) {
                // ✅ S3 FIX: Returns the resolved S3 URL via HasS3Image trait
                return $mediaFiles->has($id) ? $mediaFiles->get($id)->url : null;
            })->filter()->values();

            return $entry;
        });

        return response()->json([
            'status' => true,
            'data' => $formattedEntries
        ]);
    }

    /**
     * POST: Store EPC progress (with or without percent)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'epcentry_data_id' => 'required|exists:epcentry_data,id',
            'remarks' => 'nullable|string',
            'percent' => 'nullable|numeric|min:0|max:100',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $epcEntry = EpcEntryData::findOrFail($validated['epcentry_data_id']);
        $subPackageProject = SubPackageProject::findOrFail($epcEntry->sub_package_project_id);

        // Case B: Percent update
        if (!empty($validated['percent'])) {
            $existingPercentSum = PhysicalEpcProgress::where('epcentry_data_id', $epcEntry->id)->sum('percent');
            $remainingPercent = $epcEntry->percent - $existingPercentSum;

            if ($validated['percent'] > $remainingPercent) {
                return response()->json([
                    'status' => false,
                    'message' => "Percent cannot exceed remaining allowed percent ($remainingPercent%).",
                ], 422);
            }

            $amount = ($validated['percent'] / 100) * $subPackageProject->contract_value;

            $progress = new PhysicalEpcProgress();
            $progress->epcentry_data_id = $epcEntry->id;
            $progress->percent = $validated['percent'];
            $progress->amount = $amount;
            $progress->items = $validated['remarks'] ?? null;
            $progress->progress_submitted_date = now();
            $progress->images = [];
        } else {
            // Case A: only remarks/images (append to old entry or create fresh)
            $progress = PhysicalEpcProgress::where('epcentry_data_id', $epcEntry->id)->latest()->first();

            if (!$progress) {
                $progress = new PhysicalEpcProgress();
                $progress->epcentry_data_id = $epcEntry->id;
                $progress->percent = 0;
                $progress->amount = 0;
                $progress->items = $validated['remarks'] ?? null;
                $progress->progress_submitted_date = now();
                $progress->images = [];
            } else {
                if (!empty($validated['remarks'])) {
                    $progress->items = $validated['remarks'];
                }
            }
        }

        // Handle images
        $imageIds = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if (!$file->isValid()) continue;

                // ✅ S3 FIX: Store directly to the 's3' disk instead of 's3'
                $path = $file->store('uploads/progress_images', 's3');

                $mediaFile = MediaFile::create([
                    'path' => $path,
                    'type' => $file->getClientMimeType(),
                    'meta_data' => [
                        'subject' => 'Physical EPC Progress',
                        'epcentry_data_id' => $epcEntry->id,
                        'original_name' => $file->getClientOriginalName()
                    ],
                ]);

                $imageIds[] = $mediaFile->id;
            }
        }

        // ✅ Merge old + new images uniquely
        $progress->images = array_values(array_unique(array_merge((array) ($progress->images ?? []), $imageIds)));

        $progress->save();

        return response()->json([
            'status' => true,
            'message' => 'Progress saved successfully',
            'data' => $progress
        ]);
    }
}
