<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SafeguardEntry;
use App\Models\SocialSafeguardEntry;
use App\Models\SubPackageProject;
use App\Models\SafeguardCompliance;
use App\Models\ContractionPhase;
use App\Models\Contract;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SocialSafeguardEntryApiController extends Controller
{
    /**
     * List social safeguard entries for a project & compliance
     */
    public function index($project_id, $compliance_id, $phase_id = null, Request $request)
    {
        $subProject = SubPackageProject::findOrFail($project_id);
        $compliance = SafeguardCompliance::findOrFail($compliance_id);

        $phase_id = $phase_id ?? optional($compliance->contractionPhases()->first())->id ?? 1;
        $selectedDate = $request->input('date_of_entry', now()->format('Y-m-d'));

        $entries = SafeguardEntry::with(['socialSafeguardEntries', 'contractionPhase'])
            ->where([
                'sub_package_project_id' => $project_id,
                'safeguard_compliance_id' => $compliance_id,
                'contraction_phase_id' => $phase_id,
            ])
            ->get();

        $entries = $this->processEntries($entries, $selectedDate);

        return response()->json([
            'status'       => true,
            'subProject'   => $subProject,
            'compliance'   => $compliance,
            'phase_id'     => $phase_id,
            'selectedDate' => $selectedDate,
            'entries'      => $entries,
        ]);
    }

    /**
     * Save or update a social safeguard entry
     * REF: Enforced DB Transactions & direct S3 disk usage.
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'entry_id' => 'required|exists:safeguard_entries,id',
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'social_compliance_id' => 'required|exists:safeguard_compliances,id',
            'contraction_phase_id' => 'required|exists:contraction_phases,id',
            'yes_no' => 'nullable|string',
            'remarks' => 'nullable|string',
            'validity_date' => 'nullable|date',
            'date_of_entry' => 'nullable|date',
            'photos_documents_case_studies.*' => 'nullable|file|max:10240', // 10MB limit for API
        ]);

        $entry = SafeguardEntry::findOrFail($validated['entry_id']);
        $date = $validated['date_of_entry'] ?? now()->format('Y-m-d');

        try {
            DB::beginTransaction();

            $social = SocialSafeguardEntry::firstOrNew([
                'safeguard_entry_id' => $entry->id,
                'date_of_entry'      => $date,
            ]);

            // Defensively cast to array
            $mediaIds = is_array($social->photos_documents_case_studies) 
                        ? $social->photos_documents_case_studies 
                        : json_decode($social->photos_documents_case_studies ?? '[]', true);

            if ($request->hasFile('photos_documents_case_studies')) {
                foreach ($request->file('photos_documents_case_studies') as $file) {
                    // Upload directly to S3
                    $path = $file->store('media_files', 's3');

                    if (!$path) {
                        throw new \Exception("Failed to upload file to S3: " . $file->getClientOriginalName());
                    }

                    $media = MediaFile::create([
                        'path'      => $path,
                        'type'      => $file->getClientMimeType(),
                        'meta_data' => ['name' => $file->getClientOriginalName()],
                    ]);
                    $mediaIds[] = $media->id;
                }
            }

            $social->fill($validated);
            $social->photos_documents_case_studies = array_values(array_unique($mediaIds));
            $social->save();

            DB::commit();

            return response()->json([
                'status'    => true,
                'social_id' => $social->id,
                'message'   => 'Entry saved successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Safeguard Save Error: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Failed to save entry due to server error.'
            ], 500);
        }
    }

    /**
     * Delete a social safeguard entry
     */
    public function destroy($id)
    {
        // NOTE: In a complete system, consider deleting the associated S3 files here 
        // to prevent orphaned files, similar to destroyMedia in the web controller.
        SocialSafeguardEntry::findOrFail($id)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Safeguard entry deleted successfully.',
        ]);
    }

    /**
     * Overview of all sub-package projects & compliance
     */
    public function overview(Request $request)
    {
        $date = $request->input('date_of_entry', now()->format('Y-m-d'));

        $subProjects = SubPackageProject::with(['safeguardEntries.socialSafeguardEntries'])
            ->orderBy('name')
            ->get();

        $compliances = SafeguardCompliance::orderBy('name')->get();

        $statusMap = [];
        foreach ($subProjects as $project) {
            foreach ($compliances as $compliance) {
                $done = $project->safeguardEntries
                    ->where('safeguard_compliance_id', $compliance->id)
                    ->filter(function ($entry) use ($date) {
                        return $entry->socialSafeguardEntries()
                            ->whereDate('date_of_entry', '<=', $date)
                            ->exists();
                    })
                    ->count() > 0;

                $statusMap[$project->id][$compliance->id] = $done;
            }
        }

        return response()->json([
            'status'      => true,
            'date'        => $date,
            'subProjects' => $subProjects,
            'compliances' => $compliances,
            'statusMap'   => $statusMap,
        ]);
    }

    /**
     * Upload media files for a social safeguard entry
     * REF: Enforced DB Transactions & direct S3 disk usage.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'social_id' => 'required|exists:social_safeguard_entries,id',
            'media_files.*' => 'required|file|max:10240',
        ]);

        try {
            DB::beginTransaction();

            $socialEntry = SocialSafeguardEntry::findOrFail($request->social_id);
            
            $mediaIds = is_array($socialEntry->photos_documents_case_studies) 
                        ? $socialEntry->photos_documents_case_studies 
                        : json_decode($socialEntry->photos_documents_case_studies ?? '[]', true);

            foreach ($request->file('media_files') as $file) {
                // Upload directly to S3
                $path = $file->store('uploads', 's3');

                if (!$path) {
                    throw new \Exception("Failed to upload file to S3: " . $file->getClientOriginalName());
                }

                $media = MediaFile::create([
                    'path'      => $path,
                    'type'      => $file->getClientMimeType(),
                    'meta_data' => ['name' => $file->getClientOriginalName()],
                ]);

                $mediaIds[] = $media->id;
            }

            $socialEntry->photos_documents_case_studies = array_values(array_unique($mediaIds));
            $socialEntry->save();

            DB::commit();

            // Fetch uploaded files and format response leveraging the Model's S3 accessor
            $uploadedFiles = MediaFile::whereIn('id', $mediaIds)
                ->get()
                ->map(function ($media) {
                    return [
                        'id'       => $media->id,
                        'url'      => $media->url, // Utilizes getUrlAttribute() from HasS3Image trait
                        'name'     => $media->meta_data['name'] ?? 'File',
                        'type'     => $media->type,
                        'meta_data'=> $media->meta_data,
                    ];
                });

            return response()->json([
                'status'    => true,
                'files'     => $uploadedFiles,
                'social_id' => $socialEntry->id,
                'message'   => 'Files uploaded successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Media Upload Error: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Failed to upload files due to server error.'
            ], 500);
        }
    }

    /**
     * Helpers
     */
    private function processEntries($entries, $selectedDate)
    {
        return $entries->sortBy(function ($entry) {
                return $entry->sl_no;
            })
            ->values()
            ->map(function ($entry) use ($selectedDate) {
                $phase = $entry->contractionPhase;

                if ($phase && $phase->is_one_time) {
                    $socialEntries = collect([
                        $entry->socialSafeguardEntries()
                              ->whereDate('date_of_entry', '<=', $selectedDate)
                              ->latest('date_of_entry')
                              ->first()
                    ])->filter();
                } else {
                    $socialEntries = $entry->socialSafeguardEntries()
                        ->whereDate('date_of_entry', '<=', $selectedDate)
                        ->orderBy('date_of_entry', 'desc')
                        ->get();
                }

                $entry->socialEntries = $socialEntries;
                $entry->social       = $socialEntries->first();
                $entry->is_locked    = $this->computeLocked($entry, $entry->social);
                $entry->gallery      = $this->loadGallery($entry->social);

                return $entry;
            });
    }

    private function computeLocked($entry, $social)
    {
        $hasValidity = $entry->is_validity && $social && $social->validity_date;
        $oneTime     = $entry->contractionPhase ? $entry->contractionPhase->is_one_time : false;

        if ($oneTime) {
            return $social ? ($hasValidity ? Carbon::parse($social->validity_date)->isFuture() : true) : false;
        }

        return $hasValidity && Carbon::parse($social->validity_date)->isFuture();
    }

    private function loadGallery($social)
    {
        // Defensively decode if it's a JSON string
        $photoIds = is_array($social?->photos_documents_case_studies)
            ? $social->photos_documents_case_studies
            : json_decode($social?->photos_documents_case_studies ?? '[]', true);

        if (empty($photoIds)) {
            return collect();
        }

        return MediaFile::whereIn('id', $photoIds)
            ->get()
            ->map(function ($media) {
                return [
                    'id'       => $media->id,
                    'url'      => $media->url, // Utilizes getUrlAttribute() from HasS3Image trait
                    'name'     => $media->meta_data['name'] ?? 'File',
                    'type'     => $media->type,
                    'meta_data'=> $media->meta_data,
                ];
            });
    }

    private function naturalSort($aSl, $bSl)
    {
        $aParts = explode('.', $aSl);
        $bParts = explode('.', $bSl);

        foreach ($aParts as $i => $part) {
            $aNum = is_numeric($part) ? intval($part) : $part;
            $bNum = isset($bParts[$i]) ? (is_numeric($bParts[$i]) ? intval($bParts[$i]) : $bParts[$i]) : null;

            if ($bNum === null) return 1;
            if ($aNum === $bNum) continue;

            return $aNum < $bNum ? -1 : 1;
        }

        return count($aParts) <=> count($bParts);
    }
}