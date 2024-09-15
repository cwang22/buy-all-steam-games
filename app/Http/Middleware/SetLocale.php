<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->input('locale');
        if ($locale) {
            app()->setLocale($locale);
        }
        return $next($request);
    }
}
