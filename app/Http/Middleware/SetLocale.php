<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale') ?: $request->route('lang') ?: $request->segment(1);
        $locale = $locale === 'hi' ? 'hi' : 'en';

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);
        $request->attributes->set('locale', $locale);

        return $next($request);
    }
}
