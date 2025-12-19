<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhysicalEpcProgress;
use App\Models\EpcEntryData;
use App\Models\MediaFile;
use App\Models\SubPackageProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhysicalEpcProgressApiController extends Controller
{
    /**
     * GET: Fetch EPC progress data with remarks & images
     */
    public function indexWithEntries(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|integer'
        ]);

        // Fetch all EPC entries under this sub-project
        $epcEntries = EpcEntryData::where('sub_package_project_id', $request->sub_package_project_id)
            ->orderBy('sl_no')
            ->get()
            ->map(function ($entry) {
                // Calculate already submitted percent
                $submittedPercent = PhysicalEpcProgress::where('epcentry_data_id', $entry->id)->sum('percent');

                $entry->remaining_percent = $entry->percent - $submittedPercent;

                // Include latest remarks/images if exists
                $lastProgress = PhysicalEpcProgress::where('epcentry_data_id', $entry->id)->latest()->first();
                $entry->latest_remarks = $lastProgress->items ?? null;
                $entry->latest_images = MediaFile::whereIn('id', (array) ($lastProgress->images ?? []))
                    ->pluck('path')
                    ->map(fn($path) => asset('storage/' . $path));

                return $entry;
            });

        return response()->json([
            'status' => true,
            'data' => $epcEntries
        ]);
    }

    public function index(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|integer'
        ]);

        $progressEntries = PhysicalEpcProgress::with(['epcEntryData'])
            ->whereHas('epcEntryData', function ($q) use ($request) {
                $q->where('sub_package_project_id', $request->sub_package_project_id);
            })
            ->latest()
            ->get()
            ->map(function ($entry) {
                $entry->image_urls = MediaFile::whereIn('id', (array) $entry->images)->pluck('path')->map(function ($path) {
                    return asset('storage/' . $path);
                });
                return $entry;
            });

        return response()->json([
            'status' => true,
            'data' => $progressEntries
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
                $path = $file->store('progress_images', 'public');

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

        // Merge old + new images
        $progress->images = array_merge($progress->images ?? [], $imageIds);

        $progress->save();

        return response()->json([
            'status' => true,
            'message' => 'Progress saved successfully',
            'data' => $progress
        ]);
    }
}
