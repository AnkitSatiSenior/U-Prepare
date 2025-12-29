<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePackageProjectRequest;
use App\Http\Requests\Admin\UpdatePackageProjectRequest;
use App\Models\{
    PackageProject,
    Project,
    ProjectsCategory,
    SubCategory,
    District,
    Department,
    SubDepartment, // ✅ added
    Assembly,
    Constituency,
    SubPackageProject,
    GeographyDistrict,
    GeographyBlock,
    PackageComponent,
    Contract,
};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PackageProjectController extends Controller
{
    public function index(Request $request): View
    {
        $packageProjects = PackageProject::with([
            'project:id,name,budget',
            'category:id,name',
            'subCategory:id,name',
            'department:id,name',
            'subDepartment:id,name', // ✅ eager load
            'vidhanSabha:id,name',
            'lokSabha:id,name',
            'district:id,name',
            'block:id,name',
            'procurementDetail:id,package_project_id,method_of_procurement,type_of_procurement_id,publication_date',
            'workPrograms:id,package_project_id,procurement_details_id,name_work_program,weightage,days,start_date,planned_date',
            'subProjects:id,project_id,name',
            'contracts:id,project_id,contract_number,contract_value',
        ])
            ->withCount('workPrograms')
            ->applyFilters($request->all())
            ->latest()
            ->get();

        // dropdowns for filters
        $departments = Department::select('id', 'name')->get();
        $subDepartments = SubDepartment::select('id', 'name')->get(); // ✅ added
        $districts = District::select('id', 'name')->get();
        $components = PackageComponent::select('id', 'name')->get();
        $categories = ProjectsCategory::select('id', 'name')->get();
        $subCategories = SubCategory::select('id', 'name')->get();
        $blocks = GeographyBlock::select('id', 'name')->get();
        $vidhanSabhas = Constituency::select('id', 'name')->get();
        $lokSabhas = Constituency::select('id', 'name')->get();

        return view(
            'admin.package-projects.index',
            compact(
                'packageProjects',
                'departments',
                'subDepartments', // ✅ pass to view
                'districts',
                'categories',
                'subCategories',
                'blocks',
                'vidhanSabhas',
                'lokSabhas',
                'components',
            ),
        );
    }

    public function create(): View
    {
        return view('admin.package-projects.create', $this->formData());
    }

    public function store(StorePackageProjectRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('dec_document_path')) {
            $data['dec_document_path'] = $request->file('dec_document_path')->store('package-projects/dec-documents', 'public');
        }

        if ($request->hasFile('hpc_document_path')) {
            $data['hpc_document_path'] = $request->file('hpc_document_path')->store('package-projects/hpc-documents', 'public');
        }

        $data['status'] = $data['status'] ?? PackageProject::STATUS_PENDING_PROCUREMENT;

        PackageProject::create($data);

        return redirect()->route('admin.package-projects.index')->with('success', 'Package project created successfully.');
    }

    public function show(PackageProject $packageProject): View
    {
        $packageProject->load([
            'project:id,name,budget',
            'category:id,name',
            'subCategory:id,name',
            'department:id,name',
            'subDepartment:id,name', // ✅ added
            'vidhanSabha:id,name',
            'lokSabha:id,name',
            'district:id,name',
            'block:id,name',
            'procurementDetail',
            'workPrograms',
            'subProjects:id,project_id,name',
        ]);

        $contract = Contract::withBasicRelations()->where('project_id', $packageProject->id)->first();

        return view('admin.package-projects.show', compact('packageProject', 'contract'));
    }

    public function edit(PackageProject $packageProject): View
    {
        return view('admin.package-projects.edit', array_merge(['packageProject' => $packageProject, 'assembly' => Assembly::all()], $this->formData()));
    }

    public function update(UpdatePackageProjectRequest $request, PackageProject $packageProject): RedirectResponse
    {
        $data = $request->validated();

        // Handle DEC Document
        if ($request->hasFile('dec_document_path')) {
            if (!empty($packageProject->dec_document_path) && Storage::disk('public')->exists($packageProject->dec_document_path)) {
                Storage::disk('public')->delete($packageProject->dec_document_path);
            }
            $data['dec_document_path'] = $request->file('dec_document_path')->store('package-projects/dec-documents', 'public');
        }

        // Handle HPC Document
        if ($request->hasFile('hpc_document_path')) {
            if (!empty($packageProject->hpc_document_path) && Storage::disk('public')->exists($packageProject->hpc_document_path)) {
                Storage::disk('public')->delete($packageProject->hpc_document_path);
            }
            $data['hpc_document_path'] = $request->file('hpc_document_path')->store('package-projects/hpc-documents', 'public');
        }

        $data['status'] = $data['status'] ?? $packageProject->status;

        $packageProject->update($data);

        return redirect()->route('admin.package-projects.index')->with('success', 'Package project updated successfully.');
    }

    public function destroy(PackageProject $packageProject): RedirectResponse
    {
        $documents = array_filter([$packageProject->dec_document_path, $packageProject->hpc_document_path]);
        if (!empty($documents)) {
            Storage::disk('public')->delete($documents);
        }

        $packageProject->delete();

        return redirect()->route('admin.package-projects.index')->with('success', 'Package project deleted successfully.');
    }

    private function formData(): array
    {
        return [
            'projects' => Project::select('id', 'name', 'budget')->get(),
            'categories' => ProjectsCategory::all(),
            'subCategories' => SubCategory::all(),
            'departments' => Department::all(),
            'subDepartments' => SubDepartment::all(), // ✅ added
            'constituencies' => Constituency::all(),
            'districts' => GeographyDistrict::all(),
            'blocks' => GeographyBlock::all(),
            'components' => PackageComponent::all(),
            'statuses' => [PackageProject::STATUS_PENDING_PROCUREMENT, PackageProject::STATUS_PENDING_CONTRACT, PackageProject::STATUS_PENDING_ACTIVITY, PackageProject::STATUS_IN_PROGRESS, PackageProject::STATUS_CANCEL, PackageProject::STATUS_REBID, PackageProject::STATUS_REMOVED],
        ];
    }
}
