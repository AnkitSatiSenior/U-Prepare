<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DashboardRole;
use App\Models\Role;

class RoleDashboardController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $data = DashboardRole::with('role')->get();

        return view('admin.role_dashboards.index', compact('roles', 'data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'department' => 'required|string|in:all,department',
        ]);

        DashboardRole::create($request->only('role_id', 'department'));

        return redirect()->route('admin.role_dashboards.index')->with('success', 'Role Dashboard added successfully!');
    }

    public function update(Request $request, DashboardRole $role_dashboard)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'department' => 'required|string|in:all,department',
        ]);

        $role_dashboard->update($request->only('role_id', 'department'));

        return redirect()->route('admin.role_dashboards.index')->with('success', 'Role Dashboard updated successfully!');
    }

    public function destroy(DashboardRole $role_dashboard)
    {
        try {
            $role_dashboard->delete();
            return redirect()->route('admin.role_dashboards.index')->with('success', 'Role Dashboard deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.role_dashboards.index')->with('error', 'Failed to delete this record.');
        }
    }
}
