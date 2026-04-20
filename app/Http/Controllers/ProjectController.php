<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\FundingAgency;
use App\Http\Requests\StoreProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UpdateProjectRequest;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('fundingAgency')->latest()->paginate(15);
        return view('admin.project.index', compact('projects'));
    }

    public function create()
    {
        $agencies = FundingAgency::where('is_active', true)->orderBy('name')->get();
        
        // Example districts for Uttarakhand dropdown
        $districts = ['Dehradun', 'Haridwar', 'Nainital', 'Almora', 'Pauri', 'Tehri', 'Chamoli', 'Rudraprayag', 'Uttarkashi', 'Bageshwar', 'Champawat', 'Pithoragarh', 'Udham Singh Nagar'];
        
        return view('admin.project.create', compact('agencies', 'districts'));
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
            return back()->withInput()->with('error', 'Failed to create project: ' . $e->getMessage());
        }
    }

    public function show(Project $project)
    {
        $project->load('fundingAgency');
        return view('admin.project.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $agencies = FundingAgency::where('is_active', true)->get();
        $districts = ['Dehradun', 'Haridwar', 'Nainital', 'Almora', 'Pauri', 'Tehri', 'Chamoli', 'Rudraprayag', 'Uttarkashi', 'Bageshwar', 'Champawat', 'Pithoragarh', 'Udham Singh Nagar'];
        
        return view('admin.project.edit', compact('project', 'agencies', 'districts'));
    }

public function update(UpdateProjectRequest $request, Project $project)
{
    try {
        DB::transaction(function () use ($request, $project) {
            // We use validated() to ensure only allowed fields are updated
            $project->update($request->validated());
        });

        return redirect()->route('admin.project.index')
            ->with('success', 'Project "' . $project->name . '" updated successfully.');
            
    } catch (\Exception $e) {
        \Log::error("EAP Project Update Failed: " . $e->getMessage());
        
        return back()->withInput()->with('error', 'Update failed. Please check the logs for details.');
    }
}

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.project.index')->with('success', 'Project moved to trash.');
    }
}