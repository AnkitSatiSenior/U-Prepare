<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubPackageProjectTest;
use App\Models\SubPackageProject;
use App\Models\SubPackageProjectTestType;
use Illuminate\Http\Request;

class SubPackageProjectTestTypeController extends Controller
{
    /**
     * Display a listing of the test types.
     */
    public function index()
    {
        $testTypes = SubPackageProjectTestType::latest()->paginate(10);

        return view('admin.sub_package_project_test_types.index', compact('testTypes'));
    }

    /**
     * Store a newly created test type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_package_project_test_types,name',
        ]);

        $testType = SubPackageProjectTestType::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => 'Test Type created successfully.',
                'data' => $testType
            ]);
        }

        return redirect()->route('admin.sub_package_project_test_types.index')
                         ->with('success', 'Test Type created successfully.');
    }

    /**
     * Return a specific test type for editing (AJAX).
     */
    public function edit(SubPackageProjectTestType $subPackageProjectTestType)
    {
        return response()->json($subPackageProjectTestType);
    }

    /**
     * Update a specific test type.
     */
    public function update(Request $request, SubPackageProjectTestType $subPackageProjectTestType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_package_project_test_types,name,' . $subPackageProjectTestType->id,
        ]);

        $subPackageProjectTestType->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => 'Test Type updated successfully.',
                'data' => $subPackageProjectTestType
            ]);
        }

        return redirect()->route('admin.sub_package_project_test_types.index')
                         ->with('success', 'Test Type updated successfully.');
    }

    /**
     * Soft delete a specific test type.
     */
    public function destroy(SubPackageProjectTestType $subPackageProjectTestType)
    {
        $subPackageProjectTestType->delete();

        if (request()->ajax()) {
            return response()->json(['success' => 'Test Type deleted successfully.']);
        }

        return redirect()->route('admin.sub_package_project_test_types.index')
                         ->with('success', 'Test Type deleted successfully.');
    }

    /**
     * Optional: Restore a soft-deleted test type
     */
    public function restore($id)
    {
        $testType = SubPackageProjectTestType::onlyTrashed()->findOrFail($id);
        $testType->restore();

        return redirect()->route('admin.sub_package_project_test_types.index')
                         ->with('success', 'Test Type restored successfully.');
    }
}
