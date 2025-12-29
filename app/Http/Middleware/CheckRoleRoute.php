<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\RoleRoute;

class CheckRoleRoute
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if (!$user || !$user->role_id) {
            \Log::warning('403: Guest or user without role tried to access ' . Route::currentRouteName());
            return redirect()->back()->with('error', 'Access Denied! Ask Admin For Permission.');
        }

        $currentRouteName = Route::currentRouteName();

        if (!$currentRouteName) {
            \Log::info("Route with no name accessed by user {$user->id} (role_id: {$user->role_id})");
            return $next($request);
        }

        $allowedRoutes = cache()->remember(
            "role_routes_{$user->role_id}",
            now()->addMinutes(10),
            fn() => RoleRoute::where('role_id', $user->role_id)->pluck('route_name')->toArray()
        );

        if (!in_array($currentRouteName, $allowedRoutes)) {
            \Log::warning("403: User {$user->id} (role_id: {$user->role_id}) tried to access {$currentRouteName}. Allowed: " . implode(', ', $allowedRoutes));
            return redirect()->back()->with('error', 'Access Denied! Ask Admin For Permission.');
        }

        \Log::info("✅ User {$user->id} (role_id: {$user->role_id}) accessed {$currentRouteName}");
        return $next($request);
    }
}
