<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\FundingAgency;
use App\Http\Requests\StoreProjectRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    private const UTTARAKHAND_DISTRICTS = [
        'Dehradun',
        'Haridwar',
        'Nainital',
        'Almora',
        'Pauri',
        'Tehri',
        'Chamoli',
        'Rudraprayag',
        'Uttarkashi',
        'Bageshwar',
        'Champawat',
        'Pithoragarh',
        'Udham Singh Nagar',
    ];

    public function index()
    {
        $projects = Project::query()
            ->select([
                'id',
                'name',
                'project_short_name',
                'funding_agency_id',
                'outlay_inr',
                'is_dli_based',
                'created_at',
            ])
            ->with(['fundingAgency:id,name'])
            ->latest()
            ->paginate(15);
        return view('admin.project.index', compact('projects'));
    }

    public function create()
    {
        $agencies = Cache::remember('funding_agencies.active.v1', 3600, function () {
            return FundingAgency::query()
                ->select(['id', 'name'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });

        $districts = self::UTTARAKHAND_DISTRICTS;
        $project = new Project();

        return view('admin.project.create', compact('agencies', 'districts', 'project'));
    }

    public function store(StoreProjectRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Project::create($request->validated());
            });

            return redirect()->route('admin.project.index')
                ->with('success', 'EAP Project "' . $request->name . '" has been created successfully.');
        } catch (\Exception $e) {
            Log::error('EAP Project Create Failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('error', 'Failed to create project: ' . $e->getMessage());
        }
    }

    public function show(Project $project)
    {
        $project->loadMissing('fundingAgency');
        return view('admin.project.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $agencies = Cache::remember('funding_agencies.active.v1', 3600, function () {
            return FundingAgency::query()
                ->select(['id', 'name'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });

        $districts = self::UTTARAKHAND_DISTRICTS;
        
        return view('admin.project.edit', compact('project', 'agencies', 'districts'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        try {
            DB::transaction(function () use ($request, $project) {
                $project->update($request->validated());
            });

            return redirect()->route('admin.project.index')
                ->with('success', 'Project "' . $project->name . '" updated successfully.');
        } catch (\Exception $e) {
            Log::error('EAP Project Update Failed: ' . $e->getMessage(), ['exception' => $e, 'project_id' => $project->id]);
            return back()->withInput()->with('error', 'Update failed. Please try again.');
        }
    }

    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return redirect()->route('admin.project.index')->with('success', 'Project moved to trash.');
        } catch (\Exception $e) {
            Log::error('EAP Project Delete Failed: ' . $e->getMessage(), ['exception' => $e, 'project_id' => $project->id]);
            return back()->with('error', 'Delete failed. Please try again.');
        }
    }
}
