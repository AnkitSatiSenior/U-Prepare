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
     */
    public function indexWithEntries(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|integer',
        ]);

        $boqEntries = BoqEntryData::where('sub_package_project_id', $request->sub_package_project_id)
            ->orderBy('sl_no')
            ->get()
            ->map(function ($entry) {
                $submittedQty = PhysicalBoqProgress::where('boq_entry_id', $entry->id)->sum('qty');
                $remainingQty = $entry->qty - $submittedQty;

                $lastProgress = PhysicalBoqProgress::where('boq_entry_id', $entry->id)->latest()->first();

                $entry->remaining_qty = $remainingQty;
                $entry->latest_media = MediaFile::whereIn('id', (array) ($lastProgress->media ?? []))
                    ->get()
                    ->map(fn($file) => [
                        'id' => $file->id,
                        'url' => asset('storage/' . $file->path),
                        'meta_data' => $file->meta_data,
                    ]);

                return $entry;
            });

        return response()->json([
            'status' => true,
            'data' => $boqEntries,
        ]);
    }

    /**
     * GET: Fetch all progress entries for a sub-project
     */
    public function index(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|integer',
        ]);

        $progressEntries = PhysicalBoqProgress::whereHas('boqEntry', fn($q) => $q->where('sub_package_project_id', $request->sub_package_project_id))
            ->latest()
            ->get()
            ->map(function ($entry) {
                $entry->media_files = MediaFile::whereIn('id', (array) ($entry->media ?? []))
                    ->get()
                    ->map(fn($file) => [
                        'id' => $file->id,
                        'url' => asset('storage/' . $file->path),
                        'meta_data' => $file->meta_data,
                    ]);
                return $entry;
            });

        return response()->json([
            'status' => true,
            'data' => $progressEntries,
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

        // If qty is provided, check remaining
        if (isset($validated['qty'])) {
            $remainingQty = $this->getRemainingQty($boqEntry->id);
            if ($validated['qty'] > $remainingQty) {
                return response()->json([
                    'status' => false,
                    'message' => "Quantity cannot exceed remaining allowed quantity ($remainingQty).",
                ], 422);
            }
        }

        // Get latest progress for this BOQ entry
        $progress = PhysicalBoqProgress::where('boq_entry_id', $boqEntry->id)->latest()->first();

        if (!$progress) {
            $progress = new PhysicalBoqProgress();
            $progress->boq_entry_id = $boqEntry->id;
            $progress->sub_package_project_id = $boqEntry->sub_package_project_id;
            $progress->qty = $validated['qty'] ?? 0;
            $progress->amount = isset($validated['qty']) ? $validated['qty'] * $boqEntry->rate : 0;
            $progress->progress_submitted_date = now();
            $progress->media = [];
        } else {
            // Only update qty and amount if qty is provided
            if (isset($validated['qty'])) {
                $progress->qty = $validated['qty'];
                $progress->amount = $validated['qty'] * $boqEntry->rate;
            }
        }

        // Handle media (append to existing media)
        $mediaIds = $this->handleMedia($request, $boqEntry->id, $validated['remarks'] ?? null);
        $progress->media = array_merge($progress->media ?? [], $mediaIds);

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
    private function getRemainingQty($boqEntryId)
    {
        $boqEntry = BoqEntryData::findOrFail($boqEntryId);
        $submittedQty = PhysicalBoqProgress::where('boq_entry_id', $boqEntryId)->sum('qty');
        return $boqEntry->qty - $submittedQty;
    }

    private function handleMedia(Request $request, $boqEntryId, $remarks = null)
    {
        $mediaIds = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('boq_progress_images', 'public');

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
