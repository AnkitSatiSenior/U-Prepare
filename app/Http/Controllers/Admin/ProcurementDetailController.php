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
                ->route('admin.procurement-details.show', $packageProject->procurementDetail)
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
    // Check if procurement details already exist
    if ($packageProject->procurementDetail()->exists()) {
        return redirect()
            ->route('admin.procurement-details.show', $packageProject->procurementDetail)
            ->with('warning', 'Procurement details already exist.');
    }

    // Validate incoming request
    $validated = $this->validateProcurementDetails($request);

    try {
        // Handle file upload if exists
        if ($request->hasFile('publication_document')) {
            $validated['publication_document_path'] = $this->storePublicationDocument($request);
        }

        // Create procurement detail
        $procurementDetail = $packageProject->procurementDetail()->create($validated);

        return redirect()
            ->route('admin.procurement-details.show', $procurementDetail)
            ->with('success', 'Procurement details created successfully.');

    } catch (\Exception $e) {
        // Log detailed error for debugging
        Log::error('Failed to create procurement details', [
            'package_project_id' => $packageProject->id,
            'request_data' => $request->except(['publication_document']),
            'exception_message' => $e->getMessage(),
            'exception_trace' => $e->getTraceAsString(),
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
            if ($request->hasFile('publication_document')) {
                $this->deleteExistingDocument($procurementDetail);
                $validated['publication_document_path'] = $this->storePublicationDocument($request);
            }

            $procurementDetail->update($validated);

            return redirect()
                ->route('admin.procurement-details.show', $procurementDetail)
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

            $this->deleteExistingDocument($procurementDetail);
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
            'publication_date'           => 'nullable|date',
            'publication_document'       => 'nullable|file|mimes:pdf,doc,docx',
            'tender_fee'                 => 'nullable|numeric|min:0',
            'earnest_money_deposit'      => 'nullable|numeric|min:0',
            'bid_validity_days'          => 'nullable|integer|min:0',
            'emd_validity_days'          => 'nullable|integer|min:0',
        ]);
    }

    /**
     * Store publication document.
     */
    protected function storePublicationDocument(Request $request): string
    {
        return $request->file('publication_document')->store('procurement_docs', 'public');
    }

    /**
     * Delete existing document.
     */
    protected function deleteExistingDocument(ProcurementDetail $procurementDetail): void
    {
        if ($procurementDetail->publication_document_path && 
            Storage::disk('public')->exists($procurementDetail->publication_document_path)) {
            Storage::disk('public')->delete($procurementDetail->publication_document_path);
        }
    }
}
