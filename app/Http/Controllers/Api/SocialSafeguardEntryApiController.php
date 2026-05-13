<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlreadyDefineSafeguardEntry;
use App\Models\ContractionPhase;
use App\Models\MediaFile;
use App\Models\SafeguardCompliance;
use App\Models\SafeguardEntry;
use App\Models\SocialSafeguardEntry;
use App\Models\SubPackageProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SocialSafeguardEntryApiController extends Controller
{
    /**
     * List social safeguard master entries for a project, compliance and phase.
     */
    public function index(Request $request, int $project_id, int $compliance_id, ?int $phase_id = null)
    {
        $subProject = SubPackageProject::findOrFail($project_id);
        $compliance = SafeguardCompliance::findOrFail($compliance_id);

        $phase_id ??= $compliance->contractionPhases()->first()?->id ?? 1;
        $selectedDate = $request->input('date_of_entry', now()->format('Y-m-d'));

        $entries = AlreadyDefineSafeguardEntry::with(['safeguardCompliance', 'contractionPhase', 'category'])
            ->where('safeguard_compliance_id', $compliance_id)
            ->where('contraction_phase_id', $phase_id)
            ->orderBy('id', 'asc')
            ->orderBy('order_by', 'asc')
            ->get();

        $socialEntries = SocialSafeguardEntry::with(['masterSafeguard', 'contractionPhase'])
            ->where('sub_package_project_id', $project_id)
            ->where('social_compliance_id', $compliance_id)
            ->where('contraction_phase_id', $phase_id)
            ->whereDate('date_of_entry', '<=', $selectedDate)
            ->get()
            ->groupBy('already_define_safeguard_entry_id');

        $entries = $this->attachSocialEntries($entries, $socialEntries, $selectedDate);

        return response()->json([
            'status' => true,
            'subProject' => $subProject,
            'compliance' => $compliance,
            'phase_id' => (int) $phase_id,
            'selectedDate' => $selectedDate,
            'entries' => $entries,
        ]);
    }

    /**
     * Show a single social safeguard entry.
     */
    public function show(int $id)
    {
        $socialEntry = SocialSafeguardEntry::with(['masterSafeguard', 'subPackageProject', 'socialCompliance', 'contractionPhase'])
            ->findOrFail($id);

        $socialEntry->gallery = $this->loadGallery($socialEntry);

        return response()->json([
            'status' => true,
            'entry' => $socialEntry,
        ]);
    }

    /**
     * Save or update a social safeguard entry for the submitted date.
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'already_define_safeguard_entry_id' => 'nullable|exists:already_define_safeguard_entries,id',
            'entry_id' => 'nullable|integer',
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'social_compliance_id' => 'required|exists:safeguard_compliances,id',
            'contraction_phase_id' => 'required|exists:contraction_phases,id',
            'yes_no' => 'nullable|string',
            'remarks' => 'nullable|string',
            'validity_date' => 'nullable|date',
            'date_of_entry' => 'nullable|date',
            'photos_documents_case_studies.*' => 'nullable|file|max:10240',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$this->resolveMasterEntryId($request)) {
                $validator->errors()->add('already_define_safeguard_entry_id', 'A valid safeguard master entry is required.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $masterEntryId = $this->resolveMasterEntryId($request);
        $date = $validated['date_of_entry'] ?? now()->format('Y-m-d');

        try {
            DB::beginTransaction();

            $social = SocialSafeguardEntry::firstOrNew([
                'already_define_safeguard_entry_id' => $masterEntryId,
                'sub_package_project_id' => $validated['sub_package_project_id'],
                'social_compliance_id' => $validated['social_compliance_id'],
                'contraction_phase_id' => $validated['contraction_phase_id'],
                'date_of_entry' => $date,
            ]);

            $mediaIds = $this->mediaIds($social->photos_documents_case_studies);
            $mediaIds = array_merge($mediaIds, $this->storeUploadedMedia($request, 'photos_documents_case_studies'));

            $payload = [
                'already_define_safeguard_entry_id' => $masterEntryId,
                'sub_package_project_id' => $validated['sub_package_project_id'],
                'social_compliance_id' => $validated['social_compliance_id'],
                'contraction_phase_id' => $validated['contraction_phase_id'],
                'date_of_entry' => $date,
                'photos_documents_case_studies' => array_values(array_unique($mediaIds)),
            ];

            foreach (['yes_no', 'remarks', 'validity_date'] as $field) {
                if (array_key_exists($field, $validated)) {
                    $payload[$field] = $validated[$field];
                }
            }

            $social->fill($payload);
            $social->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'social_id' => $social->id,
                'message' => 'Entry saved successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API Safeguard Save Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to save entry due to server error.',
            ], 500);
        }
    }

    /**
     * Update an existing social safeguard entry.
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'yes_no' => 'nullable|string',
            'remarks' => 'nullable|string',
            'validity_date' => 'nullable|date',
            'date_of_entry' => 'nullable|date',
            'photos_documents_case_studies.*' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $social = SocialSafeguardEntry::findOrFail($id);
            $mediaIds = $this->mediaIds($social->photos_documents_case_studies);
            $mediaIds = array_merge($mediaIds, $this->storeUploadedMedia($request, 'photos_documents_case_studies'));

            $social->fill($validated);
            $social->photos_documents_case_studies = array_values(array_unique($mediaIds));
            $social->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'social_id' => $social->id,
                'message' => 'Entry updated successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API Safeguard Update Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to update entry due to server error.',
            ], 500);
        }
    }

    /**
     * Delete a social safeguard entry.
     */
    public function destroy(int $id)
    {
        SocialSafeguardEntry::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Safeguard entry deleted successfully.',
        ]);
    }

    /**
     * Overview of sub-package projects and compliance status.
     */
    public function overview(Request $request)
    {
        $date = $request->date_of_entry
            ? Carbon::parse($request->date_of_entry)->format('Y-m-d')
            : now()->format('Y-m-d');

        $subProjects = SubPackageProject::whereHas('packageProject', function ($query) {
            $query->where('safeguard_exists', true);
        })
            ->orderBy('name')
            ->get();

        $compliances = SafeguardCompliance::orderBy('name')->get();
        $contractionPhases = ContractionPhase::orderBy('name')->get();

        $masterSafeguards = AlreadyDefineSafeguardEntry::where('is_validity', 1)
            ->get()
            ->groupBy('safeguard_compliance_id');

        $socialEntries = SocialSafeguardEntry::whereDate('date_of_entry', '<=', $date)
            ->get()
            ->groupBy(['sub_package_project_id', 'already_define_safeguard_entry_id']);

        $statusMap = [];

        foreach ($subProjects as $project) {
            foreach ($compliances as $compliance) {
                $done = false;
                $complianceSafeguards = $masterSafeguards[$compliance->id] ?? collect();

                foreach ($complianceSafeguards as $safeguard) {
                    if (isset($socialEntries[$project->id][$safeguard->id])) {
                        $done = true;
                        break;
                    }
                }

                $statusMap[$project->id][$compliance->id] = $done;
            }
        }

        return response()->json([
            'status' => true,
            'date' => $date,
            'subProjects' => $subProjects,
            'compliances' => $compliances,
            'safeguardCompliances' => $compliances,
            'contractionPhases' => $contractionPhases,
            'statusMap' => $statusMap,
        ]);
    }

    /**
     * Upload media files for a social safeguard entry.
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'social_id' => 'required|exists:social_safeguard_entries,id',
            'media_files' => 'required',
            'media_files.*' => 'file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $socialEntry = SocialSafeguardEntry::findOrFail($request->social_id);
            $mediaIds = $this->mediaIds($socialEntry->photos_documents_case_studies);
            $mediaIds = array_merge($mediaIds, $this->storeUploadedMedia($request, 'media_files'));

            $socialEntry->photos_documents_case_studies = array_values(array_unique($mediaIds));
            $socialEntry->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'files' => $this->mediaResponse($mediaIds),
                'social_id' => $socialEntry->id,
                'message' => 'Files uploaded successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API Media Upload Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to upload files due to server error.',
            ], 500);
        }
    }

    /**
     * Delete a media file and remove references from social safeguard entries.
     */
    public function destroyMedia(int $id)
    {
        $media = MediaFile::find($id);

        if (!$media) {
            return response()->json([
                'status' => false,
                'message' => 'Media not found.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            SocialSafeguardEntry::whereJsonContains('photos_documents_case_studies', $id)
                ->get()
                ->each(function ($entry) use ($id) {
                    $entry->photos_documents_case_studies = array_values(array_diff(
                        $this->mediaIds($entry->photos_documents_case_studies),
                        [$id]
                    ));
                    $entry->save();
                });

            $path = $media->path;
            $media->delete();

            if ($path && Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'File deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API Media Deletion Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete media.',
            ], 500);
        }
    }

    private function attachSocialEntries($entries, $socialEntries, string $selectedDate)
    {
        return $entries->map(function ($entry) use ($socialEntries, $selectedDate) {
            $entrySocialEntries = $socialEntries[$entry->id] ?? collect();

            if ($entry->contractionPhase?->is_one_time) {
                $entrySocialEntries = collect([$entrySocialEntries->sortByDesc('date_of_entry')->first()])->filter();
            } else {
                $entrySocialEntries = $entrySocialEntries->sortByDesc('date_of_entry')->values();
            }

            $social = $entrySocialEntries->first();

            $entry->socialEntries = $entrySocialEntries;
            $entry->social = $social;
            $entry->has_entry = (bool) $social;
            $entry->is_filled = $entrySocialEntries->isNotEmpty();
            $entry->is_locked = $this->computeLocked($entry, $social);
            $entry->gallery = $this->loadGallery($social);
            $entry->date_of_entry = $selectedDate;

            return $entry;
        });
    }

    private function computeLocked($entry, $social): bool
    {
        if (!$social) {
            return false;
        }

        if (in_array((int) $social->yes_no, [0, 3], true)) {
            return false;
        }

        $hasValidity = $entry->is_validity && $social->validity_date;
        $oneTime = $entry->contractionPhase?->is_one_time ?? false;

        if ($oneTime) {
            return $hasValidity ? Carbon::parse($social->validity_date)->isFuture() : true;
        }

        return $hasValidity && Carbon::parse($social->validity_date)->isFuture();
    }

    private function loadGallery($social)
    {
        $photoIds = $this->mediaIds($social?->photos_documents_case_studies);

        if (empty($photoIds)) {
            return collect();
        }

        return $this->mediaResponse($photoIds);
    }

    private function mediaResponse(array $mediaIds)
    {
        if (empty($mediaIds)) {
            return collect();
        }

        return MediaFile::whereIn('id', $mediaIds)
            ->get()
            ->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->url,
                    'name' => $media->meta_data['name'] ?? 'File',
                    'type' => $media->type,
                    'meta_data' => $media->meta_data,
                ];
            });
    }

    private function mediaIds($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        $decoded = json_decode($value ?? '[]', true);

        return is_array($decoded) ? array_values(array_filter($decoded)) : [];
    }

    private function storeUploadedMedia(Request $request, string $field): array
    {
        if (!$request->hasFile($field)) {
            return [];
        }

        $files = is_array($request->file($field))
            ? $request->file($field)
            : [$request->file($field)];

        $mediaIds = [];

        foreach ($files as $file) {
            $path = $file->store('media_files', 's3');

            if (!$path) {
                throw new \RuntimeException('Failed to upload file to S3: ' . $file->getClientOriginalName());
            }

            $media = MediaFile::create([
                'path' => $path,
                'type' => $file->getClientMimeType(),
                'meta_data' => ['name' => $file->getClientOriginalName()],
            ]);

            $mediaIds[] = $media->id;
        }

        return $mediaIds;
    }

    private function resolveMasterEntryId(Request $request): ?int
    {
        if ($request->filled('already_define_safeguard_entry_id')) {
            return AlreadyDefineSafeguardEntry::whereKey($request->input('already_define_safeguard_entry_id'))->value('id');
        }

        if (!$request->filled('entry_id')) {
            return null;
        }

        $entryId = $request->input('entry_id');

        if ($masterId = AlreadyDefineSafeguardEntry::whereKey($entryId)->value('id')) {
            return $masterId;
        }

        return SafeguardEntry::whereKey($entryId)->value('already_define_safeguard_entry_id');
    }
}
