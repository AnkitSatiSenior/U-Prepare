<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubPackageProjectTestReport;
use App\Models\SubPackageProjectTest;
use App\Models\SubPackageProject;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;


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


    // Ensure your model is imported: use App\Models\SubPackageProjectTestReport;

    public function store(Request $request)
    {
        // 1. Strict Validation: Enforce a max size at the application layer.
        // 'max:10240' limits the file to 10MB. Adjust this to match your business requirements.
        $validated = $request->validate([
            'test_id' => 'required|exists:sub_package_project_tests,id',
            'report'  => 'nullable|string',
            'file'    => 'nullable|file|mimes:pdf,jpg,png,docx|max:10240', // Application-level size limit
            'remark'  => 'nullable|string',
        ]);

        $validated['approved_by'] = auth()->id();

        try {
            // 2. Infrastructure Operation: Handle S3 upload safely
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('test_reports', 's3');

                if (!$path) {
                    // Fail fast if the disk is unreachable or out of space
                    throw new \RuntimeException('Failed to write file to S3 storage.');
                }

                $validated['file'] = $path;
            }

            // 3. Database Persistence
            $report = SubPackageProjectTestReport::create($validated);

            // 4. Clean API Contract Response
            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully.',
                'data'    => [
                    'test_id'     => $report->test_id,
                    'report_file' => $report->file ? $report->url : null
                ]
            ], 201); // 201 Created is the correct REST status for resource creation

        } catch (\Exception $e) {
            // 5. Fault Tolerance: Log the real error, return a safe error to the client
            Log::error('Report Upload Failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'test_id' => $request->input('test_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An internal server error occurred while saving the report.'
            ], 500);
        }
    }
}
