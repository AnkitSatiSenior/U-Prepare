<?php

namespace App\Http\Controllers\Admin;

use App\Models\Designation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DesignationController extends Controller
{
    /**
     * Display listing, ordered by hierarchy (Level).
     */
    public function index(): View
    {
        // Principal Tip: Using the scope we added to the model
        // Orders by Level (Highest first) then by Title
        $designations = Designation::orderBy('level', 'desc')
            ->orderBy('title', 'asc')
            ->get();

        return view('admin.designations.index', compact('designations'));
    }

    public function create(): View
    {
        return view('admin.designations.form');
    }

    /**
     * Store a newly created designation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:designations,title',
            'level' => 'required|integer|min:0|max:1000', // Added hierarchy validation
        ]);

        try {
            Designation::create($validated);
            
            return redirect()
                ->route('admin.designations.index')
                ->with('success', 'Designation created successfully!');
        } catch (\Exception $e) {
            // Log the error for internal monitoring
            \Log::error("Designation Creation Failed: " . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while creating the designation.');
        }
    }

    public function edit(Designation $designation): View
    {
        return view('admin.designations.form', compact('designation'));
    }

    /**
     * Update the specified designation.
     */
    public function update(Request $request, Designation $designation): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:designations,title,' . $designation->id,
            'level' => 'required|integer|min:0|max:1000', // Added hierarchy validation
        ]);

        try {
            $designation->update($validated);

            return redirect()
                ->route('admin.designations.index')
                ->with('success', 'Designation updated successfully!');
        } catch (\Exception $e) {
            \Log::error("Designation Update Failed ID {$designation->id}: " . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while updating.');
        }
    }

    /**
     * Remove the specified designation with safety checks.
     */
    public function destroy(Designation $designation): RedirectResponse
    {
        try {
            // Safety Check: Prevent deletion if users are assigned to this designation
            if ($designation->users()->exists()) {
                return back()->with('error', 'Cannot delete: This designation is currently assigned to users.');
            }

            $designation->delete();

            return redirect()
                ->route('admin.designations.index')
                ->with('success', 'Designation deleted successfully!');
        } catch (\Exception $e) {
            \Log::error("Designation Deletion Failed ID {$designation->id}: " . $e->getMessage());

            return back()->with('error', 'Error deleting designation: System integrity check failed.');
        }
    }
}