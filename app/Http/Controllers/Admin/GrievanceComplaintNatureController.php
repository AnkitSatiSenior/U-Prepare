<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrievanceComplaintNature;
use Illuminate\Http\Request;

class GrievanceComplaintNatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $natures = GrievanceComplaintNature::latest()->get();

        return view('admin.grievance_complaint_nature.index', compact('natures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.grievance_complaint_nature.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:grievance_complaint_nature,slug'],
        ]);

        GrievanceComplaintNature::create($request->only('name', 'slug'));

        return redirect()->route('admin.grievance-complaint-nature.index')
            ->with('success', 'Complaint nature created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GrievanceComplaintNature $grievanceComplaintNature)
    {
        return view('admin.grievance_complaint_nature.edit', compact('grievanceComplaintNature'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GrievanceComplaintNature $grievanceComplaintNature)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:grievance_complaint_nature,slug,' . $grievanceComplaintNature->id],
        ]);

        $grievanceComplaintNature->update($request->only('name', 'slug'));

        return redirect()->route('admin.grievance-complaint-nature.index')
            ->with('success', 'Complaint nature updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GrievanceComplaintNature $grievanceComplaintNature)
    {
        $grievanceComplaintNature->delete();

        return redirect()->route('admin.grievance-complaint-nature.index')
            ->with('success', 'Complaint nature deleted successfully.');
    }
}
