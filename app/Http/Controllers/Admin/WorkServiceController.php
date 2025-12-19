<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkService;
use App\Models\Department;
use Illuminate\Http\Request;

class WorkServiceController extends Controller
{
    public function index()
    {
        // List all work services with their department
        $workServices = WorkService::with('department')->paginate(10);
        return view('admin.work_services.index', compact('workServices'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.work_services.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        WorkService::create($request->only('name', 'department_id'));

        return redirect()->route('admin.work_services.index')->with('success', 'Work Service created successfully.');
    }

    public function edit(WorkService $workService)
    {
        $departments = Department::all();
        return view('admin.work_services.edit', compact('workService', 'departments'));
    }

    public function update(Request $request, WorkService $workService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        $workService->update($request->only('name', 'department_id'));

        return redirect()->route('admin.work_services.index')->with('success', 'Work Service updated successfully.');
    }

    public function destroy(WorkService $workService)
    {
        $workService->delete();

        return redirect()->route('admin.work_services.index')->with('success', 'Work Service deleted successfully.');
    }
}
