<?php

namespace App\Http\Controllers;

use App\Models\PackageProject;
use App\Models\SocialSafeguardEntry;
use App\Models\MediaFile;
use App\Models\SubPackageProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PackageProjectDocumentController extends Controller
{
    /**
     * Show package project documents and social safeguard gallery
     */
  // In your Controller

public function index(Request $request, $id)
{
    $package = PackageProject::with([
        'procurementDetail',
        'workPrograms',
        'subProjects.epcEntries.physicalEpcProgresses', 
    ])->findOrFail($id);

    $documents = $this->getPackageDocuments($package);
    $subProjectDocs = $this->getSubProjectDocuments($package);

    // Filters
    $subProjectId = $request->input('sub_package_project_id');
    $complianceId = $request->input('safeguard_compliance_id');
    $phaseId = $request->input('contraction_phase_id');

    // ✅ Get Split Galleries
    $galleries = $this->getSafeguardGalleries($subProjectId, $complianceId, $phaseId);

    return view('admin.package-projects.documents', [
        'package' => $package,
        'documents' => $documents,
        'subProjectDocs' => $subProjectDocs,
        // Pass them separately
        'envGallery' => $galleries['environmental'],
        'socGallery' => $galleries['social'],
        'subProjectId' => $subProjectId,
        'complianceId' => $complianceId,
        'phaseId' => $phaseId
    ]);
}

private function getSafeguardGalleries($subProjectId = null, $complianceId = null, $phaseId = null): array
{
    // 1. Fetch Entries
    $query = SocialSafeguardEntry::with(['masterSafeguard', 'contractionPhase']);

    if ($subProjectId) $query->where('sub_package_project_id', $subProjectId);
    if ($complianceId) $query->where('social_compliance_id', $complianceId);
    if ($phaseId) $query->where('contraction_phase_id', $phaseId);

    $entries = $query->orderBy('date_of_entry', 'desc')->get();

    // 2. Extract Media IDs
    $allMediaIds = [];
    foreach ($entries as $entry) {
        $ids = $entry->photos_documents_case_studies;
        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            $ids = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : explode(',', $ids);
        }
        if (is_array($ids)) {
            $cleanIds = array_filter(array_map('intval', $ids));
            $allMediaIds = array_merge($allMediaIds, $cleanIds);
            $entry->temp_media_ids = $cleanIds; 
        } else {
            $entry->temp_media_ids = [];
        }
    }

    // 3. Fetch Media Files
    $mediaFilesDict = MediaFile::whereIn('id', array_unique($allMediaIds))->get()->keyBy('id');

    // 4. Initialize Result Arrays
    $result = [
        'environmental' => [],
        'social' => []
    ];

    foreach ($entries as $entry) {
        $entryMediaIds = $entry->temp_media_ids ?? [];
        if (empty($entryMediaIds)) continue;

        // Map Media
        $mappedMedia = [];
        foreach ($entryMediaIds as $mediaId) {
            if (isset($mediaFilesDict[$mediaId])) {
                $file = $mediaFilesDict[$mediaId];
                $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
                $mimeType = strtolower($file->type ?? '');
                $isImage = str_contains($mimeType, 'image') || in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

                $mappedMedia[] = [
                    'id' => $file->id,
                    'full_url' => asset('storage/app/public/' . $file->path),
                    'is_image' => $isImage,
                    'extension' => $extension,
                    'original_name' => $file->meta_data['name'] ?? basename($file->path),
                ];
            }
        }

        if (empty($mappedMedia)) continue;

        // Determine Type (1 = Env, 2 = Social)
        $sId = $entry->masterSafeguard->safeguard_compliance_id ?? 0;
        
        // Define data structure
        $data = [
            'id' => $entry->id,
            'item_description' => $entry->masterSafeguard->item_description ?? 'Description Not Available',
            'phase' => $entry->contractionPhase->name ?? 'N/A',
            'yes_no' => (int) $entry->yes_no,
            'remarks' => $entry->remarks,
            'media' => $mappedMedia
        ];
        
        $dateKey = \Carbon\Carbon::parse($entry->date_of_entry)->format('Y-m-d');

        // Assign to specific array based on ID
        if ($sId == 1) {
            $result['environmental'][$dateKey][] = $data;
        } elseif ($sId == 2) {
            $result['social'][$dateKey][] = $data;
        }
    }

    return $result;
}
    public function subProjectDocuments($subProjectId)
    {
        $subProject = SubPackageProject::with(['packageProject', 'epcEntries.physicalEpcProgresses'])->findOrFail($subProjectId);

        $documents = [];
        $seenUrls = [];

        $addDocument = function (&$documents, &$seenUrls, $name, $url, $date = null, $type = 'pdf') {
            if ($url && !in_array($url, $seenUrls)) {
                $documents[] = compact('name', 'url', 'date', 'type');
                $seenUrls[] = $url;
            }
        };

        foreach ($subProject->epcEntries as $entry) {
            foreach ($entry->physicalEpcProgresses as $progress) {
                foreach ($progress->mediaFiles as $file) {
                    $addDocument($documents, $seenUrls, $file->name, $file->url, $progress->progress_submitted_date, $file->type);
                }
            }
        }

        return view('admin.sub-projects.documents', compact('subProject', 'documents'));
    }
    /** -------------------- Helper Functions -------------------- */

    /**
     * Collect package-level documents
     */
    private function getPackageDocuments(PackageProject $package): array
    {
        $documents = [];
        $seenUrls = [];

        $addDocument = function (&$documents, &$seenUrls, $name, $url, $date = null, $type = 'pdf') {
            if ($url && !in_array($url, $seenUrls)) {
                $documents[] = compact('name', 'url', 'date', 'type');
                $seenUrls[] = $url;
            }
        };

        // PackageProject docs
        $addDocument($documents, $seenUrls, 'DEC Approval', $package->dec_document_path ? Storage::url($package->dec_document_path) : null, $package->dec_approval_date);
        $addDocument($documents, $seenUrls, 'HPC Approval', $package->hpc_document_path ? Storage::url($package->hpc_document_path) : null, $package->hpc_approval_date);

        // ProcurementDetail docs
        if ($package->procurementDetail) {
            $path = $package->procurementDetail->publication_document_path ? Storage::url($package->procurementDetail->publication_document_path) : null;
            $addDocument($documents, $seenUrls, 'Publication Document', $path, $package->procurementDetail->publication_date);
        }

        // WorkPrograms docs
        foreach ($package->workPrograms as $wp) {
            $addDocument($documents, $seenUrls, "Procurement Bid ({$wp->name_work_program})", $wp->procurement_bid_document_url, $wp->planned_date);
            $addDocument($documents, $seenUrls, "Pre-Bid Minutes ({$wp->name_work_program})", $wp->pre_bid_minutes_document_url, $wp->planned_date);
        }

        return $documents;
    }

    /**
     * Collect EPC + subproject-level documents
     */
    private function getSubProjectDocuments(PackageProject $package): array
    {
        $subProjectDocs = [];

        foreach ($package->subProjects as $subProject) {
            $documents = [];
            $seenUrls = [];

            $addDocument = function (&$documents, &$seenUrls, $name, $url, $date = null, $type = 'pdf') {
                if ($url && !in_array($url, $seenUrls)) {
                    $documents[] = compact('name', 'url', 'date', 'type');
                    $seenUrls[] = $url;
                }
            };

            foreach ($subProject->epcEntries as $epcEntry) {
                foreach ($epcEntry->physicalEpcProgresses as $progress) {
                    foreach ($progress->mediaFiles as $media) {
                        // ✅ accessor from model
                        $type = str_contains($media->type, 'image') ? 'image' : 'pdf';
                        $addDocument($documents, $seenUrls, "EPC Progress ({$epcEntry->activity_name}) - " . ($media->meta_data['name'] ?? $media->id), $media->url, $progress->progress_submitted_date, $type);
                    }
                }
            }

            $subProjectDocs[] = [
                'subProject' => $subProject,
                'documents' => $documents,
            ];
        }

        return $subProjectDocs;
    }

    /**
     * Collect social safeguard gallery data
     */

}
