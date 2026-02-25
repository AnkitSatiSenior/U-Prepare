<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProcurementDetail;
use App\Models\PackageProject;
use App\Models\TypeOfProcurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcurementDetailController extends Controller
{
    /**
     * Display a listing of package projects with procurement details.
     */
    public function index()
    {
        $packageProjects = PackageProject::with([
            'procurementDetail',
            'project',
            'category',
            'subCategory',
            'department',
            'vidhanSabha',
            'district',
            'block'
        ])->latest()->get();

        return view('admin.procurement_details.index', compact('packageProjects'));
    }

    /**
     * Show form for creating procurement details for a specific package project.
     */
    public function create(PackageProject $packageProject)
    {
        if ($packageProject->procurementDetail()->exists()) {
            return redirect()
                ->route('admin.procurement-details.index', $packageProject->procurementDetail)
                ->with('warning', 'Procurement details already exist for this package project.');
        }

        $methodsOfProcurement = $packageProject->category?->methods_of_procurement ?? [];
        $typesOfProcurement = TypeOfProcurement::all();

        return view('admin.procurement_details.create', [
            'packageProject' => $packageProject,
            'methodsOfProcurement' => $methodsOfProcurement,
            'typesOfProcurement' => $typesOfProcurement
        ]);
    }

    /**
     * Store new procurement details.
     */
    public function store(Request $request, PackageProject $packageProject)
    {
        if ($packageProject->procurementDetail()->exists()) {
            return redirect()
                ->route('admin.procurement-details.index', $packageProject->procurementDetail)
                ->with('warning', 'Procurement details already exist.');
        }

        $validated = $this->validateProcurementDetails($request);

        try {
            $this->handleFileUploads($request, $validated);

            $procurementDetail = $packageProject->procurementDetail()->create($validated);

            return redirect()
                ->route('admin.procurement-details.index')
                ->with('success', 'Procurement details created successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to create procurement details', [
                'package_project_id' => $packageProject->id,
                // Exclude file uploads from log to prevent massive log entries
                'request_data' => $request->except([
                    'publication_document', 'technical_eval_document', 
                    'financial_eval_document', 'loa_issued_document'
                ]),
                'exception_message' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to create procurement details. Please try again.');
        }
    }

    /**
     * Display a procurement detail.
     */
    public function show(ProcurementDetail $procurementDetail)
    {
        $procurementDetail->load([
            'packageProject.project',
            'packageProject.category',
            'packageProject.subCategory',
            'packageProject.department',
            'packageProject.vidhanSabha',
            'packageProject.district',
            'packageProject.block',
            'typeOfProcurement'
        ]);

        return view('admin.procurement_details.show', compact('procurementDetail'));
    }

    /**
     * Show form for editing procurement details.
     */
    public function edit(ProcurementDetail $procurementDetail)
    {
        $methodsOfProcurement = $procurementDetail->packageProject->category?->methods_of_procurement ?? [];
        $typesOfProcurement = TypeOfProcurement::all();

        return view('admin.procurement_details.edit', [
            'procurementDetail' => $procurementDetail,
            'methodsOfProcurement' => $methodsOfProcurement,
            'typesOfProcurement' => $typesOfProcurement
        ]);
    }

    /**
     * Update procurement details.
     */
    public function update(Request $request, ProcurementDetail $procurementDetail)
    {
        $validated = $this->validateProcurementDetails($request);

        try {
            // Pass the existing model so old files get deleted when replaced
            $this->handleFileUploads($request, $validated, $procurementDetail);

            $procurementDetail->update($validated);

            return redirect()
                ->route('admin.procurement-details.index')
                ->with('success', 'Procurement details updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update procurement details: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update procurement details. Please try again.');
        }
    }

    /**
     * Delete procurement details.
     */
    public function destroy(ProcurementDetail $procurementDetail)
    {
        try {
            $packageProjectId = $procurementDetail->package_project_id;

            // Delete all associated documents from storage
            $this->deleteExistingDocuments($procurementDetail);
            $procurementDetail->delete();

            return redirect()
                ->route('admin.package-projects.show', $packageProjectId)
                ->with('success', 'Procurement details deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete procurement details: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete procurement details. Please try again.');
        }
    }

    /**
     * Validate procurement details request.
     */
    protected function validateProcurementDetails(Request $request): array
    {
        return $request->validate([
            'method_of_procurement'      => 'required|string|max:255',
            'type_of_procurement_id'     => 'required|exists:type_of_procurements,id',
            
            // Dates
            'publication_date'           => 'nullable|date',
            'technical_eval_date'        => 'nullable|date',
            'financial_eval_date'        => 'nullable|date',
            'loa_issued_date'            => 'nullable|date',
            
            // Documents
            'publication_document'       => 'nullable|file|mimes:pdf,doc,docx|max:10240', // optional max size (10MB)
            'technical_eval_document'    => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'financial_eval_document'    => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'loa_issued_document'        => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            
            // Financials & Validity
            'tender_fee'                 => 'nullable|numeric|min:0',
            'earnest_money_deposit'      => 'nullable|numeric|min:0',
            'bid_validity_days'          => 'nullable|integer|min:0',
            'emd_validity_days'          => 'nullable|integer|min:0',
        ]);
    }

    /**
     * Handle upload of multiple documents automatically.
     */
    protected function handleFileUploads(Request $request, array &$validated, ?ProcurementDetail $existingDetail = null): void
    {
        $fileFields = [
            'publication_document'    => 'publication_document_path',
            'technical_eval_document' => 'technical_eval_document_path',
            'financial_eval_document' => 'financial_eval_document_path',
            'loa_issued_document'     => 'loa_issued_document_path',
        ];

        foreach ($fileFields as $inputField => $dbColumn) {
            if ($request->hasFile($inputField)) {
                // If updating, delete the old file first
                if ($existingDetail && $existingDetail->$dbColumn) {
                    Storage::disk('public')->delete($existingDetail->$dbColumn);
                }
                
                $validated[$dbColumn] = $request->file($inputField)->store('procurement_docs', 'public');
            }
        }
    }

    /**
     * Delete all existing documents associated with the record.
     */
    protected function deleteExistingDocuments(ProcurementDetail $procurementDetail): void
    {
        $documentPaths = [
            $procurementDetail->publication_document_path,
            $procurementDetail->technical_eval_document_path,
            $procurementDetail->financial_eval_document_path,
            $procurementDetail->loa_issued_document_path,
        ];

        foreach ($documentPaths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}