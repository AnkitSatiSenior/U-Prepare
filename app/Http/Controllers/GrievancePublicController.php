<?php

namespace App\Http\Controllers;

use App\Helpers\StaticDataHelper;
use App\Models\{Grievance, District, GeographyBlock, GrievanceComplaintDetail, GrievanceComplaintNature, GrievanceAssignment, Department, PackageProject, GrievanceLog, User};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GrievancePublicController extends Controller
{
    /**
     * Show grievance registration form
     */
    public function create()
    {
        $districts = District::with('blocks')->orderBy('name')->get();
        $categories = GrievanceComplaintNature::with('details')->orderBy('name')->get();
        $projects = PackageProject::select('id', 'package_name', 'district_id')->orderBy('package_name')->get();
        $typology = StaticDataHelper::typology();

        return view('grievances.create', compact('categories', 'typology', 'districts', 'projects'));
    }

    /**
     * Fetch complaint subcategories by category ID
     */
    public function getSubCats(Request $request)
    {
        if (!$request->filled('category_id')) {
            return response()->json(['ok' => 0, 'msg' => 'Invalid request!']);
        }

        $category = GrievanceComplaintNature::find($request->category_id);
        if (!$category) {
            return response()->json(['ok' => 0, 'msg' => 'Category not found!']);
        }

        $scats = GrievanceComplaintDetail::where('nature_id', $category->id)->get(['id', 'name']);

        return response()->json([
            'ok' => 1,
            'msg' => "{$scats->count()} subcategories fetched.",
            'data' => $scats,
        ]);
    }

    /**
     * Get blocks of a district
     */
    public function getBlocks(Request $request)
    {
        if (!$request->filled('slug')) {
            return response()->json(['ok' => 0, 'msg' => 'Invalid request!']);
        }

        $district = District::where('slug', $request->slug)->first();
        if (!$district) {
            return response()->json(['ok' => 0, 'msg' => 'District not found!']);
        }

        $data = GeographyBlock::where('district_id', $district->id)->get(['slug', 'name']);

        return response()->json([
            'ok' => 1,
            'msg' => "{$data->count()} blocks fetched.",
            'data' => $data,
        ]);
    }

    /**
     * Get projects by district ID
     */
    public function getProjects(Request $request)
    {
        if (!$request->filled('slug')) {
            return response()->json(['ok' => 0, 'msg' => 'Invalid request!']);
        }

        $projects = PackageProject::where('district_id', $request->slug)->get();

        return response()->json([
            'ok' => 1,
            'msg' => $projects->count() ? "{$projects->count()} projects fetched." : 'No projects found for this district.',
            'data' => $this->buildSluggedData($projects, true),
        ]);
    }

    /**
     * Get districts by typology slug
     */
    public function getDistricts(Request $request)
    {
        if (!$request->filled('slug')) {
            return response()->json(['ok' => 0, 'msg' => 'Invalid request!']);
        }

        $typology = $request->slug !== 'other' ? StaticDataHelper::typology($request->slug, true) : null;

        if ($typology && $typology->dept) {
            $dept = Department::where('name', $typology->dept)->first();
            $districts = PackageProject::select('district_id as id')->where('department_id', $dept->id)->distinct()->get();
            $data = District::whereIn('id', $districts->pluck('id'))->get(['name', 'slug']);
        } else {
            $data = District::select(['name', 'slug'])->get();
        }

        return response()->json([
            'ok' => 1,
            'msg' => "{$data->count()} Districts fetched successfully!",
            'data' => $data,
        ]);
    }

    /**
     * Helper: Build slugged dataset for project/district dropdowns
     */
    private function buildSluggedData($records, $encryptId = false)
    {
        $return = [];
        foreach ($records as $record) {
            if (!empty($record->package_name ?? $record->name)) {
                $return[] = (object) [
                    'name' => $record->package_name ?? $record->name,
                    'slug' => $encryptId ? encrypt($record->id) : Str::slug($record->package_name ?? $record->name),
                ];
            }
        }
        return $return;
    }

    /**
     * Search grievance by number
     */
    public function statusSearch(Request $request)
    {
        $request->validate([
            'grievance_no' => 'required|string',
        ]);

        return redirect()->route('grievances.status', $request->grievance_no);
    }

    /**
     * Store a new grievance and log it
     */
   public function store(Request $request)
{
    $request->validate([
        'full_name' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:500',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:15',
        'typology' => 'required|string',
        'category' => 'required|string',
        'subcategory' => 'required|string',
        'district' => 'required|string',
        'project' => 'nullable|string',
        'village' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:2000',
        'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,mp4|max:25120',
        'behalf' => 'required|string',
        'consent' => 'required|string',
    ]);

    try {
        $documentPath = $request->hasFile('file') 
            ? $request->file('file')->store('grievance_docs', 'public') 
            : null;

        $grievanceNo = 'GRV-' . strtoupper(Str::random(8));

        // 🔹 Fetch related display names
        $categoryName = GrievanceComplaintNature::find($request->category)?->name;
        $subcategoryName = GrievanceComplaintDetail::find($request->subcategory)?->name;
        $districtName = District::find($request->district)?->name;
        $projectName = PackageProject::find($request->project)?->package_name;

        // 🔹 Identify department
        $typologyObj = StaticDataHelper::typology($request->typology, true);
        $departmentId = null;
        $departmentName = null;

        if ($typologyObj && $typologyObj->dept) {
            $dept = Department::where('name', $typologyObj->dept)->first();
            $departmentId = $dept?->id;
            $departmentName = $dept?->name;
        }

        // 🔹 Create grievance record
        $grievance = Grievance::create([
            'grievance_no' => $grievanceNo,
            'full_name' => $request->full_name,
            'address' => $request->address,
            'email' => $request->email,
            'mobile' => $request->phone,
            'grievance_related_to' => $request->typology,
            'nature_of_complaint' => $categoryName,
            'detail_of_complaint' => $subcategoryName,
            'district' => $districtName,
            'village' => $request->village,
            'project' => $projectName,
            'description' => $request->description,
            'document' => $documentPath,
            'filing_on_behalf' => $request->behalf === 'yes' ? 1 : 0,
            'consent_from_survivor' => $request->consent === 'yes' ? 1 : 0,
            'status' => 'pending',
            'department_id' => $departmentId,
        ]);

        // 🔹 Assignment Logic (only one user)
        $assignedUser = null;
        $designationPriority = [15, 13, 22, 17, 18, 14];

        if ($departmentId) {
            // Try priority-based designations
            foreach ($designationPriority as $designationId) {
                $assignedUser = User::where('department_id', $departmentId)
                    ->where('designation_id', $designationId)
                    ->first();
                if ($assignedUser) break;
            }

            // If still no user found, assign to first user of department
            if (!$assignedUser) {
                $assignedUser = User::where('department_id', $departmentId)->first();
            }

            // Create assignment if user found
            if ($assignedUser) {
                GrievanceAssignment::create([
                    'grievance_id' => $grievance->id,
                    'assigned_to' => $assignedUser->id,
                    'assigned_by' => 1, // System/Admin
                    'department' => $departmentName,
                ]);
            }
        }

        // 🔹 Log grievance submission
        $assignedName = $assignedUser?->name ?? 'No user found';
        $logRemark = "Grievance {$grievanceNo} submitted successfully. ";

        if ($assignedUser) {
            $logRemark .= "Assigned to: <b>{$assignedName}</b> ({$departmentName}).";
        } else {
            $logRemark .= "No users found for department <b>{$departmentName}</b>.";
        }

        GrievanceLog::create([
            'grievance_id' => $grievance->id,
            'user_id' => $assignedUser?->id,
            'type' => 'log',
            'title' => 'New Grievance Submitted',
            'remark' => $logRemark,
            'document' => $documentPath,
        ]);

        Log::info("✅ Grievance {$grievanceNo} created. Assigned to: {$assignedName} ({$departmentName}).");

        return redirect()
            ->route('grievances.status', $grievance->grievance_no)
            ->with('success', "✅ Grievance submitted successfully! Your Grievance No is <b>{$grievanceNo}</b>");
    } catch (\Exception $e) {
        Log::error('❌ Grievance submission failed: ' . $e->getMessage());
        return back()->withErrors('Something went wrong. Please try again.');
    }
}


    /**
     * Check grievance status by number
     */
    public function status($grievance_no)
    {
        $grievance = Grievance::where('grievance_no', $grievance_no)
            ->with(['logs.user', 'assignments.assignedUser'])
            ->firstOrFail();

        return view('grievances.status', compact('grievance'));
    }

    /**
     * Blank status form
     */
    public function status2()
    {
        return view('grievances.status');
    }
}
