<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectSubprojectLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProjectSubprojectLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projectId = $request->get('project_id');
        $subprojectId = $request->get('subproject_id');

        $query = ProjectSubprojectLink::with(['project', 'subproject', 'user']);

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($subprojectId) {
            $query->where('subproject_id', $subprojectId);
        }

        $links = $query->orderByDesc('created_at')->get();

        // Simplified JSON — show only needed names
        $data = $links->map(function ($link) {
            return [
                'id' => $link->id,
                'project_id' => $link->project_id,
                'project_name' => optional($link->project)->package_name,
                'subproject_id' => $link->subproject_id,
                'subproject_name' => optional($link->subproject)->name,
                'user_id' => $link->user_id,
                'user_name' => optional($link->user)->name,
                'remark' => $link->remark,
                'created_at' => $link->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $data->count(),
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'nullable|exists:package_projects,id',
            'subproject_id' => 'nullable|exists:sub_package_projects,id',
            'remark' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Automatically attach authenticated user
        $data = $validator->validated();
        $data['user_id'] = Auth::id();

        $link = ProjectSubprojectLink::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Project-Subproject link created successfully.',
            'data' => [
                'id' => $link->id,
                'project_name' => optional($link->project)->package_name,
                'subproject_name' => optional($link->subproject)->name,
                'user_name' => optional($link->user)->name,
                'remark' => $link->remark,
                'created_at' => $link->created_at->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Display a specific record.
     */
    public function show($id)
    {
        $link = ProjectSubprojectLink::with(['project', 'subproject', 'user'])->find($id);

        if (!$link) {
            return response()->json([
                'success' => false,
                'message' => 'Link not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $link->id,
                'project_name' => optional($link->project)->package_name,
                'subproject_name' => optional($link->subproject)->name,
                'user_name' => optional($link->user)->name,
                'remark' => $link->remark,
                'created_at' => $link->created_at->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, $id)
    {
        $link = ProjectSubprojectLink::find($id);

        if (!$link) {
            return response()->json([
                'success' => false,
                'message' => 'Link not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'project_id' => 'sometimes|exists:package_projects,id',
            'subproject_id' => 'nullable|exists:sub_package_projects,id',
            'remark' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $link->update(array_merge(
            $validator->validated(),
            ['user_id' => Auth::id()]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Link updated successfully.',
            'data' => $link->load(['project', 'subproject', 'user']),
        ]);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id)
    {
        $link = ProjectSubprojectLink::find($id);

        if (!$link) {
            return response()->json([
                'success' => false,
                'message' => 'Link not found.',
            ], 404);
        }

        $link->delete();

        return response()->json([
            'success' => true,
            'message' => 'Link deleted successfully.',
        ]);
    }
}
