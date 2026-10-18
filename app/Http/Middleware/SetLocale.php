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
        if (in_array($locale, ['en', 'zh'], true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
