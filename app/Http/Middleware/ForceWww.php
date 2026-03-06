<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceWww
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle($request, Closure $next)
{
    $host = $request->header('host');

    // Check if we are in production and if 'www.' is missing
    if (app()->isProduction() && !str_starts_with($host, 'www.')) {
        return redirect()->to('https://www.' . $host . $request->getRequestUri(), 301);
    }

    return $next($request);
}
}
