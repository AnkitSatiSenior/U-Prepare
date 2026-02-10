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
    public function index(Request $request, $id)
    {
        $package = PackageProject::with([
            'procurementDetail',
            'workPrograms',
            'subProjects.epcEntries.physicalEpcProgresses', // ✅ we’ll use accessor for mediaFiles
        ])->findOrFail($id);

        // Package & SubProject documents
        $documents = $this->getPackageDocuments($package);
        $subProjectDocs = $this->getSubProjectDocuments($package);

        // Optional filters for safeguard gallery
        $subProjectId = $request->input('sub_package_project_id');
        $complianceId = $request->input('safeguard_compliance_id');
        $phaseId = $request->input('contraction_phase_id');

        // Unified Social Safeguard Gallery
        $gallery = $this->getSocialSafeguardGallery($subProjectId, $complianceId, $phaseId);

        return view('admin.package-projects.documents', compact('package', 'documents', 'subProjectDocs', 'gallery', 'subProjectId', 'complianceId', 'phaseId'));
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
    private function getSocialSafeguardGallery($subProjectId = null, $complianceId = null, $phaseId = null): array
    {
        $query = SocialSafeguardEntry::with('safeguardEntry');

        if ($subProjectId) {
            $query->where('sub_package_project_id', $subProjectId);
        }

        if ($complianceId) {
            $query->where('social_compliance_id', $complianceId);
        }

        if ($phaseId) {
            $query->where('contraction_phase_id', $phaseId);
        }

        $entries = $query->orderBy('date_of_entry', 'desc')->get();

        $gallery = [];
        foreach ($entries as $entry) {
            $mediaIds = is_array($entry->photos_documents_case_studies) ? $entry->photos_documents_case_studies : json_decode($entry->photos_documents_case_studies, true);

            if (!$mediaIds) {
                continue;
            }

            $mediaFiles = MediaFile::whereIn('id', $mediaIds)->get();
            if ($mediaFiles->isEmpty()) {
                continue;
            }

            $dateKey = Carbon::parse($entry->date_of_entry)->format('Y-m-d');
            $gallery[$dateKey][] = [
                'entry' => $entry,
                'media' => $mediaFiles,
                'remarks' => $entry->remarks,
                'yes_no' => $entry->yes_no,
                'item_description' => $entry->safeguardEntry?->item_description,
            ];
        }

        return $gallery;
    }
}
