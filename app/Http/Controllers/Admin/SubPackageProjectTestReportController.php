<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubPackageProjectTestReport;
use App\Models\SubPackageProjectTest;
use App\Models\SubPackageProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubPackageProjectTestReportController extends Controller
{
    public function index(int $subPackageProjectId)
    {
        $subProject = SubPackageProject::findOrFail($subPackageProjectId);

        // Get all tests with latest report
        $tests = SubPackageProjectTest::with('report')
            ->where('sub_package_project_id', $subPackageProjectId)
            ->latest()
            ->get();

        return view('admin.sub_package_project_test_reports.index', compact('subProject', 'tests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'test_id' => 'required|exists:sub_package_project_tests,id',
            'report'  => 'nullable|string',
            'file'    => 'nullable|file|mimes:pdf,jpg,png,docx',
            'remark'  => 'nullable|string',
        ]);

        $validated['approved_by'] = auth()->id();

        if ($request->hasFile('file')) {
            // ✅ S3 FIX: Store directly to the 's3' disk instead of 'public'
            $validated['file'] = $request->file('file')->store('test_reports', 's3');
        }

        $report = SubPackageProjectTestReport::create($validated);

        return response()->json([
            'success' => 'Report submitted successfully.',
            'test_id' => $validated['test_id'],
            // ✅ S3 FIX: Leverage the appended 'url' attribute from the HasS3Image trait
            'report_file' => $report->file ? $report->url : null
        ]);
    }
}