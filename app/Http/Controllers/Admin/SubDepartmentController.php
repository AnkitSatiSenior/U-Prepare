<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubDepartment;
use App\Models\Department;
use Illuminate\Http\Request;

class SubDepartmentController extends Controller
{
    public function index()
    {
        $subDepartments = SubDepartment::with('department')->latest()->get();
        return view('admin.sub_departments.index', compact('subDepartments'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.sub_departments.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        SubDepartment::create([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'status' => $request->status ?? true,
        ]);

        return redirect()->route('admin.sub-departments.index')
                         ->with('success', 'Sub Department created successfully.');
    }

    public function edit(SubDepartment $subDepartment)
    {
        $departments = Department::all();
        return view('admin.sub_departments.edit', compact('subDepartment', 'departments'));
    }

    public function update(Request $request, SubDepartment $subDepartment)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $subDepartment->update([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'status' => $request->status ?? true,
        ]);

        return redirect()->route('admin.sub-departments.index')
                         ->with('success', 'Sub Department updated successfully.');
    }

    public function destroy(SubDepartment $subDepartment)
    {
        $subDepartment->delete();
        return redirect()->route('admin.sub-departments.index')
                         ->with('success', 'Sub Department deleted successfully.');
    }
}
