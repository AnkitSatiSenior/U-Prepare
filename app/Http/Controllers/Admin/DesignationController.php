<?php

namespace App\Http\Controllers\Admin;

use App\Models\Designation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::latest()->get();
        return view('admin.designations.index', compact('designations'));
    }

    public function create()
    {
        return view('admin.designations.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|unique:designations,title',
        ]);

        try {
            Designation::create($validated);
            return redirect()->route('admin.designations.index')
                ->with('success', 'Designation created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating designation: ' . $e->getMessage());
        }
    }

    public function edit(Designation $designation)
    {
        return view('admin.designations.form', compact('designation'));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'title' => 'required|string|unique:designations,title,' . $designation->id,
        ]);

        try {
            $designation->update($validated);
            return redirect()->route('admin.designations.index')
                ->with('success', 'Designation updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating designation: ' . $e->getMessage());
        }
    }

    public function destroy(Designation $designation)
    {
        try {
            $designation->delete();
            return redirect()->route('admin.designations.index')
                ->with('success', 'Designation deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting designation: ' . $e->getMessage());
        }
    }
}