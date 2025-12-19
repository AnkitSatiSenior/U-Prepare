<?php

namespace App\Http\Controllers;

use App\Models\PermissionGroup;
use App\Models\PermissionGroupRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PermissionGroupController extends Controller
{
    /**
     * Display all permission groups with assigned routes
     */
    public function index()
    {
        $groups = PermissionGroup::with('routes')->get();

        // Fetch all app routes (GET + POST etc.)
        $allRoutes = collect(Route::getRoutes())
            ->map(function ($route) {
                return $route->getName();
            })
            ->filter() // remove null route names
            ->values();

        return view('permission-groups.index', compact('groups', 'allRoutes'));
    }

    /**
     * Create a group
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        PermissionGroup::create($request->only(['name', 'description']));

        return back()->with('success', 'Permission group created successfully!');
    }

    /**
     * Update group
     */
    public function update(Request $request, $id)
    {
        $group = PermissionGroup::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $group->update($request->only(['name', 'description']));

        return back()->with('success', 'Group updated successfully!');
    }

    /**
     * Delete group + its routes
     */
    public function destroy($id)
    {
        PermissionGroup::findOrFail($id)->delete();

        return back()->with('success', 'Group deleted successfully!');
    }

    /**
     * Assign multiple routes to the group
     */
    public function assignRoutes(Request $request, $groupId)
    {
        $request->validate([
            'routes' => 'array|required',
        ]);

        $group = PermissionGroup::findOrFail($groupId);

        // Remove existing routes, then re-add selected
        $group->routes()->delete();

        foreach ($request->routes as $routeName) {
            PermissionGroupRoute::create([
                'group_id' => $groupId,
                'route_name' => $routeName,
            ]);
        }

        return back()->with('success', 'Routes updated successfully!');
    }

    /**
     * Remove a single route
     */
    public function removeRoute($routeId)
    {
        PermissionGroupRoute::findOrFail($routeId)->delete();

        return back()->with('success', 'Route removed from group successfully!');
    }
    public function manageRoutes($groupId)
    {
        $group = PermissionGroup::with('routes')->findOrFail($groupId);

        // Extract all allowed admin routes
        $allowedRoutes = collect(\Route::getRoutes())
            ->filter(function ($route) {
                // Only routes with a name
                if (!$route->getName()) {
                    return false;
                }

                // Must contain admin. prefix → admin routes only
                if (!str_starts_with($route->getName(), 'admin.')) {
                    return false;
                }

                // Must include required middlewares
                $middlewares = $route->gatherMiddleware();

                $requiredMw = ['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role.routes'];

                // Ensure route has **ALL** required middlewares
                foreach ($requiredMw as $mw) {
                    if (!in_array($mw, $middlewares)) {
                        return false;
                    }
                }

                return true;
            })
            ->map(fn($route) => $route->getName())
            ->unique()
            ->values();

        $routeCount = $allowedRoutes->count();

        return view('permission-groups.manage-routes', [
            'group' => $group,
            'allRoutes' => $allowedRoutes,
            'routeCount' => $routeCount,
        ]);
    }

    public function saveRoutes(Request $request, $groupId)
    {
        $request->validate([
            'routes' => 'array|required',
        ]);

        $group = PermissionGroup::findOrFail($groupId);

        // Clear previous routes
        $group->routes()->delete();

        // Insert new selected routes
        foreach ($request->routes as $routeName) {
            PermissionGroupRoute::create([
                'group_id' => $groupId,
                'route_name' => $routeName,
            ]);
        }

        return back()->with('success', 'Routes updated successfully!');
    }
}
