<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubPackageProjectTest;
use App\Models\SubPackageProject;
use App\Models\SubPackageProjectTestType;
use Illuminate\Http\Request;

class SubPackageProjectTestController extends Controller
{
    /**
     * Display a paginated listing of tests for a given SubPackageProject.
     */
    public function index(int $subPackageProjectId)
    {
        $subProject = SubPackageProject::findOrFail($subPackageProjectId);

        $tests = SubPackageProjectTest::with('testType')
            ->where('sub_package_project_id', $subPackageProjectId)
            ->latest()
            ->get();

        return view('admin.sub_package_project_tests.index', compact('subProject', 'tests'));
    }

    /**
     * Store a newly created test.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'test_type_id'           => 'required|exists:sub_package_project_test_types,id',
            'test_name'              => 'required|string|max:255',
        ]);

        $testType = SubPackageProjectTestType::findOrFail($validated['test_type_id']);

        $test = SubPackageProjectTest::create([
            'sub_package_project_id' => $validated['sub_package_project_id'],
            'test_type_id'           => $testType->id,
            'test_name'              => $validated['test_name'],
            'test_type'              => $testType->name, // Or use a constant/type if needed
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Test added successfully.', 'data' => $test]);
        }

        return redirect()
            ->route('admin.sub_package_project_tests.index', $validated['sub_package_project_id'])
            ->with('success', 'Test added successfully.');
    }

    /**
     * Show the specified test for editing via AJAX.
     */
    public function edit(SubPackageProjectTest $subPackageProjectTest)
    {
        return response()->json($subPackageProjectTest);
    }

    /**
     * Update the specified test.
     */
    public function update(Request $request, SubPackageProjectTest $subPackageProjectTest)
    {
        $validated = $request->validate([
            'test_type_id' => 'required|exists:sub_package_project_test_types,id',
            'test_name'    => 'required|string|max:255',
        ]);

        $testType = SubPackageProjectTestType::findOrFail($validated['test_type_id']);

        $subPackageProjectTest->update([
            'test_type_id' => $testType->id,
            'test_name'    => $validated['test_name'],
            'test_type'    => $testType->name,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Test updated successfully.', 'data' => $subPackageProjectTest]);
        }

        return redirect()
            ->route('admin.sub_package_project_tests.index', $subPackageProjectTest->sub_package_project_id)
            ->with('success', 'Test updated successfully.');
    }

    /**
     * Soft delete the specified test.
     */
    public function destroy(SubPackageProjectTest $subPackageProjectTest)
    {
        $subPackageProjectTest->delete();

        if (request()->ajax()) {
            return response()->json(['success' => 'Test deleted successfully.']);
        }

        return redirect()
            ->route('admin.sub_package_project_tests.index', $subPackageProjectTest->sub_package_project_id)
            ->with('success', 'Test deleted successfully.');
    }

    /**
     * Restore a soft-deleted test.
     */
    public function restore(int $id)
    {
        $test = SubPackageProjectTest::onlyTrashed()->findOrFail($id);
        $test->restore();

        return redirect()
            ->route('admin.sub_package_project_tests.index', $test->sub_package_project_id)
            ->with('success', 'Test restored successfully.');
    }
}
