<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectsCategory;
use Illuminate\Http\Request;

class ProjectsCategoryController extends Controller
{
    public function index()
    {
        $categories = ProjectsCategory::latest()->paginate(10);
        return view('admin.projects-category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.projects-category.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'methods_of_procurement' => 'nullable|array',
            'status' => 'required|boolean',
        ]);

        // Convert comma-separated string to array
        if ($request->methods_of_procurement && isset($request->methods_of_procurement[0])) {
            $methods = array_map('trim', explode(',', $request->methods_of_procurement[0]));
            $validated['methods_of_procurement'] = array_filter($methods); // Remove empty values
        } else {
            $validated['methods_of_procurement'] = null;
        }

        ProjectsCategory::create($validated);

        return redirect()->route('admin.projects-category.index')->with('success', 'Category created successfully.');
    }

    public function edit(ProjectsCategory $projectsCategory)
    {
        return view('admin.projects-category.edit', compact('projectsCategory'));
    }

    public function update(Request $request, ProjectsCategory $projectsCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'methods_of_procurement' => 'nullable|array',
            'status' => 'required|boolean',
        ]);

        // Convert comma-separated string to array
        if ($request->methods_of_procurement && isset($request->methods_of_procurement[0])) {
            $methods = array_map('trim', explode(',', $request->methods_of_procurement[0]));
            $validated['methods_of_procurement'] = array_filter($methods); // Remove empty values
        } else {
            $validated['methods_of_procurement'] = null;
        }

        $projectsCategory->update($validated);

        return redirect()->route('admin.projects-category.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(ProjectsCategory $projectsCategory)
    {
        $projectsCategory->delete();

        return redirect()->route('admin.projects-category.index')->with('success', 'Category deleted successfully.');
    }
}