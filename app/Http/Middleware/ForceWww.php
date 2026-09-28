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
        // 308, not 301: a 301 makes browsers re-issue a POST as GET, which turns
        // form submits to the bare domain into 405s. 308 preserves the method.
        return redirect()->to('https://www.' . $host . $request->getRequestUri(), 308);
    }

    return $next($request);
}
}
