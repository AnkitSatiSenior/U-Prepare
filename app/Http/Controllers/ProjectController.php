<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Display a list of projects
    public function index()
    {
        $projects = Project::query()->latest()->paginate(10);

        return view('admin.project.index', compact('projects'));
    }

    // Show form to create a new project
    public function create()
    {
        return view('admin.project.create');
    }

    // Store a new project
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'budget' => 'nullable|numeric|min:0',
        ]);

        Project::create($validated);

        return redirect()->route('admin.project.index')->with('success', 'Project created successfully.');
    }

    // Show single project details
    public function show(Project $project)
    {
        return view('admin.project.show', compact('project'));
    }

    // Show form to edit project
    public function edit(Project $project)
    {
        return view('admin.project.edit', compact('project'));
    }

    // Update an existing project
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'budget' => 'nullable|numeric|min:0',
        ]);

        $project->update($validated);

        return redirect()->route('admin.project.index')->with('success', 'Project updated successfully.');
    }

    // Soft delete the project
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.project.index')->with('success', 'Project deleted successfully.');
    }
}
