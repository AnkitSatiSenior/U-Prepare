<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrievanceComplaintDetail;
use App\Models\GrievanceComplaintNature;
use Illuminate\Http\Request;

class GrievanceComplaintDetailController extends Controller
{
    /**
     * Display a listing of the details.
     */
    public function index()
    {
        $details = GrievanceComplaintDetail::with('nature')->latest()->get();
        return view('admin.grievance_details.index', compact('details'));
    }

    /**
     * Show the form for creating a new detail.
     */
    public function create()
    {
        $natures = GrievanceComplaintNature::all();
        return view('admin.grievance_details.create', compact('natures'));
    }

    /**
     * Store a newly created detail.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nature_id' => 'required|exists:grievance_complaint_nature,id',
            'name'      => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:grievance_complaint_detail,slug',
        ]);

        GrievanceComplaintDetail::create($request->only('nature_id', 'name', 'slug'));

        return redirect()->route('admin.grievance_details.index')
                         ->with('success', 'Complaint detail created successfully.');
    }

    /**
     * Show the form for editing the specified detail.
     */
    public function edit(GrievanceComplaintDetail $grievance_detail)
    {
        $natures = GrievanceComplaintNature::all();
        return view('admin.grievance_details.edit', compact('grievance_detail', 'natures'));
    }

    /**
     * Update the specified detail.
     */
    public function update(Request $request, GrievanceComplaintDetail $grievance_detail)
    {
        $request->validate([
            'nature_id' => 'required|exists:grievance_complaint_nature,id',
            'name'      => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:grievance_complaint_detail,slug,' . $grievance_detail->id,
        ]);

        $grievance_detail->update($request->only('nature_id', 'name', 'slug'));

        return redirect()->route('admin.grievance_details.index')
                         ->with('success', 'Complaint detail updated successfully.');
    }

    /**
     * Remove the specified detail.
     */
    public function destroy(GrievanceComplaintDetail $grievance_detail)
    {
        $grievance_detail->delete();
        return redirect()->route('admin.grievance_details.index')
                         ->with('success', 'Complaint detail deleted successfully.');
    }
}
