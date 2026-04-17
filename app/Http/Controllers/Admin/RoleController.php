<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of roles, ordered by level (Hierarchy).
     */
    public function index(): View
    {
        // Principal Tip: Always order your collections explicitly.
        // Higher levels usually imply higher authority.
        $roles = Role::orderBy('level', 'desc')->get();
        
        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:roles,name',
            'level' => 'required|integer|min:0|max:1000', // Added level validation
        ]);

        Role::create($validated);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:roles,name,' . $role->id,
            'level' => 'required|integer|min:0|max:1000', // Added level validation
        ]);

        $role->update($validated);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // Check for dependencies before deleting (Safety First)
        if ($role->users()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete role assigned to existing users.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted.');
    }
}