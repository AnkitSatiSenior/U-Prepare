<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhysicalBoqProgress;
use App\Models\BoqEntryData;
use App\Models\SubPackageProject;
use App\Models\MediaFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PhysicalBoqProgressController extends Controller
{
    /** ---------------------------
     * Display the BOQ progress page
     * --------------------------- */
    public function index(Request $request)
    {
        $subProjects = SubPackageProject::select('id', 'name')->get();
        $selectedProjectId = $request->input('sub_package_project_id');
        $selectedDate = $request->input('date', now()->format('Y-m-d'));

        $subProject = $selectedProjectId ? SubPackageProject::find($selectedProjectId) : null;

        $boqEntries = $selectedProjectId ? $this->getBoqEntriesGrouped($selectedProjectId) : collect();

        $physicalProgress = $selectedProjectId ? $this->getPhysicalProgressData($selectedProjectId, $selectedDate) : collect();

        return view('admin.physical_boq_progress.index', compact('subProjects', 'subProject', 'boqEntries', 'physicalProgress', 'selectedProjectId', 'selectedDate'));
    }
public function physicalProgress(Request $request)
{
    // 1️⃣ All sub-projects
    $subProjects = SubPackageProject::select('id', 'name')->get();

    // 2️⃣ Selected sub-project & date
    $selectedProjectId = $request->input('sub_package_project_id');
    $selectedDate = $request->input('date', now()->format('Y-m-d'));

    $subProject = $selectedProjectId ? SubPackageProject::find($selectedProjectId) : null;

    // 3️⃣ BOQ entries grouped by parent SL No
    $boqEntries = $selectedProjectId
        ? BoqEntryData::where('sub_package_project_id', $selectedProjectId)
            ->orderByRaw("CAST(SUBSTRING_INDEX(sl_no, '.', 1) AS UNSIGNED), sl_no")
            ->get()
            ->groupBy(fn($item) => explode('.', $item->sl_no)[0])
        : collect();

    // 4️⃣ Physical progress up to the selected date
    $physicalProgress = $selectedProjectId ? $this->getPhysicalProgressData($selectedProjectId, $selectedDate) : collect();

    // 5️⃣ Return view
    return view('admin.physical_boq_progress.index-2', compact(
        'subProjects',
        'subProject',
        'boqEntries',
        'physicalProgress',
        'selectedProjectId',
        'selectedDate'
    ));
}


    /** ---------------------------
     * AJAX: Get physical progress JSON
     * --------------------------- */
    public function getPhysicalProgress(Request $request)
    {
        $projectId = $request->sub_package_project_id;
        $selectedDate = $request->date ?? now()->format('Y-m-d');

        $subProject = SubPackageProject::find($projectId);
        $boqEntries = $this->getBoqEntriesGrouped($projectId);
        $physicalProgress = $this->getPhysicalProgressData($projectId, $selectedDate);

        return response()->json([
            'subProject' => $subProject,
            'selectedDate' => $selectedDate,
            'boqEntries' => $boqEntries,
            'physicalProgress' => $physicalProgress,
        ]);
    }

    /** ---------------------------
     * AJAX: Store or update progress
     * --------------------------- */
    public function saveProgress(Request $request)
    {
        $data = $request->validate([
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'progress_date' => 'required|date',
            'entries.*.boq_entry_id' => 'required|exists:boqentry_data,id',
            'entries.*.current_day.qty' => 'required|numeric|min:0',
            'entries.*.current_day.amount' => 'nullable|numeric|min:0',
        ]);

        foreach ($data['entries'] as $entry) {
            $boqEntry = BoqEntryData::findOrFail($entry['boq_entry_id']);

            // 🔍 Check how much is already consumed
            $alreadyEnteredQty = PhysicalBoqProgress::where('boq_entry_id', $boqEntry->id)->where('sub_package_project_id', $data['sub_package_project_id'])->whereDate('progress_submitted_date', '!=', $data['progress_date'])->sum('qty');

            // If there is already a record on this date, exclude it from sum
            $existingRecord = PhysicalBoqProgress::where('boq_entry_id', $boqEntry->id)->where('sub_package_project_id', $data['sub_package_project_id'])->whereDate('progress_submitted_date', $data['progress_date'])->first();

            if ($existingRecord) {
                $alreadyEnteredQty -= $existingRecord->qty;
            }

            $remainingQty = $boqEntry->qty - $alreadyEnteredQty;

            if ($entry['current_day']['qty'] > $remainingQty) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => "Cannot enter qty more than remaining BOQ.
                                Remaining allowed qty for Item {$boqEntry->sl_no}: {$remainingQty}",
                    ],
                    422,
                );
            }

            // ✅ Safe to save
            PhysicalBoqProgress::updateOrCreate(
                [
                    'boq_entry_id' => $boqEntry->id,
                    'sub_package_project_id' => $data['sub_package_project_id'],
                    'progress_submitted_date' => $data['progress_date'],
                ],
                [
                    'qty' => $entry['current_day']['qty'],
                    'amount' => $entry['current_day']['qty'] * $boqEntry->rate,
                ],
            );
        }

        return response()->json(['status' => 'success', 'message' => 'Progress saved successfully.']);
    }

    /** ---------------------------
     * Create page
     * --------------------------- */


    /** ---------------------------
     * Store new progress
     * --------------------------- */
    public function store(Request $request)
    {
        $validated = $this->validateProgress($request);
        $this->createOrUpdateProgress($validated);
        return redirect()->route('admin.physical_boq_progress.index')->with('success', 'Physical BOQ Progress record created successfully.');
    }

    /** ---------------------------
     * Update existing progress
     * --------------------------- */
    public function update(Request $request, PhysicalBoqProgress $physicalBoqProgress)
    {
        $validated = $this->validateProgress($request);
        $this->createOrUpdateProgress($validated, $physicalBoqProgress);
        return redirect()->route('admin.physical_boq_progress.index')->with('success', 'Physical BOQ Progress record updated successfully.');
    }

    /** ---------------------------
     * Delete progress
     * --------------------------- */
    public function destroy(Request $request, PhysicalBoqProgress $physicalBoqProgress)
    {
        $this->deleteImages($physicalBoqProgress->media);
        $physicalBoqProgress->delete();

        return redirect()->route('admin.physical_boq_progress.index')->with('success', 'Physical BOQ Progress record deleted successfully.');
    }

    /** ---------------------------
     * Bulk delete
     * --------------------------- */
    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:physical_boq_progress,id']);
        $entries = PhysicalBoqProgress::whereIn('id', $request->ids)->get();

        foreach ($entries as $entry) {
            $this->deleteImages($entry->media);
            $entry->delete();
        }

        return response()->json(['status' => 'success']);
    }

    /** ---------------------------
     * PRIVATE HELPERS
     * --------------------------- */

    // Get BOQ entries grouped by sl_no
    private function getBoqEntriesGrouped($projectId)
    {
        return BoqEntryData::where('sub_package_project_id', $projectId)->orderBy('sl_no')->get()->groupBy('sl_no');
    }

    // Calculate physical progress (previous, current, up to date)
    private function getPhysicalProgressData($projectId, $selectedDate)
    {
        $selectedDateCarbon = Carbon::parse($selectedDate);
        $boqEntries = $this->getBoqEntriesGrouped($projectId);
        $allProgress = PhysicalBoqProgress::where('sub_package_project_id', $projectId)->get()->groupBy('boq_entry_id');

        $result = [];

        foreach ($boqEntries as $entries) {
            foreach ($entries as $entry) {
                $boqId = $entry->id;
                $progressCollection = $allProgress[$boqId] ?? collect();

                $previousQty = $progressCollection->filter(fn($p) => Carbon::parse($p->progress_submitted_date)->lt($selectedDateCarbon))->sum('qty');
                $previousAmount = $progressCollection->filter(fn($p) => Carbon::parse($p->progress_submitted_date)->lt($selectedDateCarbon))->sum('amount');

                $currentRecord = $progressCollection->first(fn($p) => Carbon::parse($p->progress_submitted_date)->isSameDay($selectedDateCarbon));
                $currentQty = $currentRecord->qty ?? 0;
                $currentAmount = $currentRecord->amount ?? 0;

                $result[$boqId] = (object) [
                    'since_previous' => (object) ['qty' => $previousQty, 'amount' => $previousAmount],
                    'current_day' => (object) ['qty' => $currentQty, 'amount' => $currentAmount],
                    'up_to_date' => (object) ['qty' => $previousQty + $currentQty, 'amount' => $previousAmount + $currentAmount],
                ];
            }
        }

        return $result;
    }

    // Validate progress input
    private function validateProgress(Request $request)
    {
        return $request->validate([
            'boq_entry_id' => ['required', Rule::exists('boqentry_data', 'id')],
            'qty' => 'required|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
            'progress_submitted_date' => 'nullable|date',
            'media.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
        ]);
    }

    // Create or update a progress record
    // Create or update a progress record
    private function createOrUpdateProgress(array $validated, PhysicalBoqProgress $existing = null)
    {
        $boqEntry = BoqEntryData::findOrFail($validated['boq_entry_id']);

        // Total already entered qty (excluding current record if editing)
        $alreadyEnteredQty = PhysicalBoqProgress::where('boq_entry_id', $boqEntry->id)->when($existing, fn($q) => $q->where('id', '!=', $existing->id))->sum('qty');

        $remainingQty = $boqEntry->qty - $alreadyEnteredQty;

        // ❌ Prevent exceeding BOQ qty
        if ($validated['qty'] > $remainingQty) {
            abort(
                422,
                "Cannot enter qty more than remaining BOQ.
                Remaining allowed qty: {$remainingQty}",
            );
        }

        // Calculate amount
        $validated['amount'] = $validated['qty'] * $boqEntry->rate;

        // Handle media uploads
        $newMediaIds = $this->handleImages(request(), [
            'subject' => 'Physical BOQ Progress',
            'boq_entry_id' => $boqEntry->id,
            'item_description' => $boqEntry->item_description,
        ]);

        $validated['media'] = $existing ? array_merge($existing->media ?? [], $newMediaIds) : $newMediaIds;

        // Save record
        if ($existing) {
            $existing->update($validated);
        } else {
            PhysicalBoqProgress::create($validated);
        }
    }

    // Sum progress for a BOQ entry
    private function getTotalProgressForEntry($boqEntryId, $excludeId = null)
    {
        $query = PhysicalBoqProgress::where('boq_entry_id', $boqEntryId);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->sum('qty');
    }

    // Handle media file upload
    private function handleImages(Request $request, array $additionalMeta = []): array
    {
        $mediaIds = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('boq_progress_images', 'public');
                $mediaFile = MediaFile::create([
                    'path' => $path,
                    'type' => $file->getClientMimeType(),
                    'meta_data' => array_merge($additionalMeta, ['original_name' => $file->getClientOriginalName()]),
                ]);
                $mediaIds[] = $mediaFile->id;
            }
        }
        return $mediaIds;
    }
    /**
     * Upload new images and attach them to the latest PhysicalBoqProgress record
     * for a given sub_package_project_id.
     */
    public function uploadImagesToLastProgressBoq(Request $request)
    {
        $request->validate([
            'sub_package_project_id' => 'required|integer|exists:sub_package_projects,id',
            'media.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Find the latest PhysicalBoqProgress for the given sub_package_project_id
        $lastProgress = PhysicalBoqProgress::where('sub_package_project_id', $request->sub_package_project_id)->latest()->first();

        // If no progress exists
        if (!$lastProgress) {
            return redirect()->back()->with('error', 'Please create a physical BOQ progress entry first before uploading images.');
        }

        // Prepare metadata
        $additionalMeta = [
            'subject' => 'Physical BOQ Progress Image Upload',
            'sub_package_project_id' => $request->sub_package_project_id,
            'attached_to' => "BOQ Progress ID: {$lastProgress->id}",
        ];

        // Upload new images
        $newMediaIds = $this->handleImages($request, $additionalMeta);

        // Merge with existing media IDs
        $existingMedia = is_array($lastProgress->media) ? $lastProgress->media : json_decode($lastProgress->media, true);

        $updatedMedia = array_merge($existingMedia ?? [], $newMediaIds);

        // Update the last progress entry
        $lastProgress->update(['media' => $updatedMedia]);

        return redirect()->back()->with('success', 'Images uploaded successfully and attached to the latest Physical BOQ Progress entry.');
    }

    // Delete media files
    private function deleteImages(array $mediaIds = [])
    {
        if (!$mediaIds) {
            return;
        }
        $mediaFiles = MediaFile::whereIn('id', $mediaIds)->get();
        foreach ($mediaFiles as $file) {
            Storage::disk('public')->delete($file->path);
            $file->delete();
        }
    }
}
