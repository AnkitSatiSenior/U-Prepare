<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhysicalBoqProgress;
use App\Models\BoqEntryData;
use App\Models\MediaFile;
use Illuminate\Http\Request;

class PhysicalBoqProgressApiController extends Controller
{
    /**
     * GET: Fetch BOQ progress data with media and remarks
     * ARCHITECTURE FIX: Eliminated N+1 queries using in-memory aggregate mapping.
     */
    public function indexWithEntries(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|integer',
        ]);

        $subProjectId = $request->sub_package_project_id;

        // 1. Fetch all BOQ Entries
        $boqEntries = BoqEntryData::where('sub_package_project_id', $subProjectId)
            ->orderBy('sl_no')
            ->get();

        $boqEntryIds = $boqEntries->pluck('id')->toArray();

        // 2. Fetch ALL related progress in ONE query
        $allProgress = PhysicalBoqProgress::whereIn('boq_entry_id', $boqEntryIds)->get();

        // 3. Process aggregates and latest entries in memory (O(n))
        $progressSums = [];
        $latestProgressMap = [];
        $allMediaIds = [];

        foreach ($allProgress as $progress) {
            // Aggregate totals
            $progressSums[$progress->boq_entry_id] = ($progressSums[$progress->boq_entry_id] ?? 0) + $progress->qty;

            // Track latest entry
            if (!isset($latestProgressMap[$progress->boq_entry_id]) || $progress->created_at > $latestProgressMap[$progress->boq_entry_id]->created_at) {
                $latestProgressMap[$progress->boq_entry_id] = $progress;
            }
        }

        // 4. Extract all required media IDs for the "latest" entries
        foreach ($latestProgressMap as $latest) {
            if (!empty($latest->media)) {
                $allMediaIds = array_merge($allMediaIds, (array) $latest->media);
            }
        }

        // 5. Fetch all required Media in ONE query
        $allMediaIds = array_unique($allMediaIds);
        $mediaFiles = empty($allMediaIds) 
            ? collect() 
            : MediaFile::whereIn('id', $allMediaIds)->get()->keyBy('id');

        // 6. Map the final response without a single DB hit inside the loop
        $formattedEntries = $boqEntries->map(function ($entry) use ($progressSums, $latestProgressMap, $mediaFiles) {
            $submittedQty = $progressSums[$entry->id] ?? 0;
            $entry->remaining_qty = max(0, $entry->qty - $submittedQty); // Ensure no negative remaining
            
            $lastProgress = $latestProgressMap[$entry->id] ?? null;
            $mediaIds = $lastProgress ? (array) ($lastProgress->media ?? []) : [];

            $entry->latest_media = collect($mediaIds)->map(function ($id) use ($mediaFiles) {
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
            })->filter()->values();

            return $entry;
        });

        return response()->json([
            'status' => true,
            'data' => $formattedEntries,
        ]);
    }

    /**
     * GET: Fetch all progress entries for a sub-project
     * ARCHITECTURE FIX: Eliminated N+1 queries using batched media retrieval.
     */
    public function index(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|integer',
        ]);

        // 1. Fetch progress entries
        $progressEntries = PhysicalBoqProgress::whereHas('boqEntry', function ($q) use ($request) {
                $q->where('sub_package_project_id', $request->sub_package_project_id);
            })
            ->latest()
            ->get();

        // 2. Extract all unique media IDs
        $allMediaIds = $progressEntries->pluck('media')->flatten()->filter()->unique()->toArray();

        // 3. Fetch media in ONE query
        $mediaFiles = empty($allMediaIds) 
            ? collect() 
            : MediaFile::whereIn('id', $allMediaIds)->get()->keyBy('id');

        // 4. Map in memory
        $formattedEntries = $progressEntries->map(function ($entry) use ($mediaFiles) {
            $mediaIds = (array) ($entry->media ?? []);
            
            $entry->media_files = collect($mediaIds)->map(function ($id) use ($mediaFiles) {
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
            })->filter()->values();

            return $entry;
        });

        return response()->json([
            'status' => true,
            'data' => $formattedEntries,
        ]);
    }

    /**
     * POST: Store BOQ progress (qty optional, media with remarks inside meta)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'boq_entry_id' => 'required|exists:boqentry_data,id',
            'qty' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'media.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $boqEntry = BoqEntryData::findOrFail($validated['boq_entry_id']);

        if (isset($validated['qty'])) {
            $remainingQty = $this->getRemainingQty($boqEntry->id);
            if ($validated['qty'] > $remainingQty) {
                return response()->json([
                    'status' => false,
                    'message' => "Quantity cannot exceed remaining allowed quantity ($remainingQty).",
                ], 422);
            }
        }

        $progress = PhysicalBoqProgress::where('boq_entry_id', $boqEntry->id)->latest()->first();

        if (!$progress) {
            $progress = new PhysicalBoqProgress();
            $progress->boq_entry_id = $boqEntry->id;
            // Assuming this column exists based on your original code
            $progress->sub_package_project_id = $boqEntry->sub_package_project_id ?? null; 
            $progress->qty = $validated['qty'] ?? 0;
            $progress->amount = isset($validated['qty']) ? $validated['qty'] * $boqEntry->rate : 0;
            $progress->progress_submitted_date = now();
            $progress->media = [];
        } else {
            if (isset($validated['qty'])) {
                $progress->qty = $validated['qty'];
                $progress->amount = $validated['qty'] * $boqEntry->rate;
            }
        }

        $mediaIds = $this->handleMedia($request, $boqEntry->id, $validated['remarks'] ?? null);
        $progress->media = array_values(array_unique(array_merge((array) $progress->media, $mediaIds)));

        $progress->save();

        return response()->json([
            'status' => true,
            'message' => 'Progress saved successfully',
            'data' => $progress,
        ]);
    }

    /**
     * PRIVATE HELPERS
     */
    private function getRemainingQty(int $boqEntryId): float|int
    {
        $boqEntry = BoqEntryData::findOrFail($boqEntryId);
        $submittedQty = PhysicalBoqProgress::where('boq_entry_id', $boqEntryId)->sum('qty');
        return $boqEntry->qty - $submittedQty;
    }

    private function handleMedia(Request $request, int $boqEntryId, ?string $remarks = null): array
    {
        $mediaIds = [];
        
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if (!$file->isValid()) continue;

                // ✅ S3 FIX: Store directly to the 's3' disk instead of 'public'
                $path = $file->store('uploads/boq_progress_images', 's3');

                $mediaFile = MediaFile::create([
                    'path' => $path,
                    'type' => $file->getClientMimeType(),
                    'meta_data' => [
                        'subject' => 'Physical BOQ Progress',
                        'boq_entry_id' => $boqEntryId,
                        'original_name' => $file->getClientOriginalName(),
                        'remarks' => $remarks,
                    ],
                ]);

                $mediaIds[] = $mediaFile->id;
            }
        }
        
        return $mediaIds;
    }
}