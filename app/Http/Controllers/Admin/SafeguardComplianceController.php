<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SafeguardCompliance;
use App\Models\Role;
use App\Models\ContractionPhase;
use Illuminate\Http\Request;

class SafeguardComplianceController extends Controller
{
    /**
     * Display a listing of Safeguard Compliances
     * Eager load role to avoid N+1 queries
     */
    public function index()
    {
        $compliances = SafeguardCompliance::with('role')
            ->latest()
            ->get();

        return view('admin.safeguard_compliances.index', compact('compliances'));
    }

    /**
     * Show form for creating a new Safeguard Compliance
     */
    public function create()
    {
        $roles = Role::all();
        $phases = ContractionPhase::all();

        return view('admin.safeguard_compliances.create', compact('roles', 'phases'));
    }

    /**
     * Store a newly created Safeguard Compliance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'contraction_phase_ids' => 'nullable|array',
            'contraction_phase_ids.*' => 'exists:contraction_phases,id',
        ]);

        SafeguardCompliance::create($validated);

        return redirect()->route('admin.safeguard-compliances.index')
                         ->with('success', 'Safeguard Compliance created successfully.');
    }

    /**
     * Show form for editing an existing Safeguard Compliance
     */
    public function edit(SafeguardCompliance $safeguardCompliance)
    {
        $roles = Role::all();
        $phases = ContractionPhase::all();

        return view('admin.safeguard_compliances.edit', compact('safeguardCompliance', 'roles', 'phases'));
    }

    /**
     * Update the specified Safeguard Compliance
     */
    public function update(Request $request, SafeguardCompliance $safeguardCompliance)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'contraction_phase_ids' => 'nullable|array',
            'contraction_phase_ids.*' => 'exists:contraction_phases,id',
        ]);

        $safeguardCompliance->update($validated);

        return redirect()->route('admin.safeguard-compliances.index')
                         ->with('success', 'Safeguard Compliance updated successfully.');
    }

    /**
     * Soft delete the specified Safeguard Compliance
     */
    public function destroy(SafeguardCompliance $safeguardCompliance)
    {
        $safeguardCompliance->delete();

        return redirect()->route('admin.safeguard-compliances.index')
                         ->with('success', 'Safeguard Compliance deleted successfully.');
    }
}
