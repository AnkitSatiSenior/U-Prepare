<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuthToken;
use Carbon\Carbon;

class AuthTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Authorization token missing'], 401);
        }

        $authToken = AuthToken::with('user')
            ->where('token', $token)
         ->where('is_valid', true)
            ->where('valid_until', '>=', Carbon::now())
            ->first();

        if (!$authToken) {
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }

        // Bind user to request (like auth()->user())
        $request->setUserResolver(fn () => $authToken->user);

        return $next($request);
    }
}
