<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // اللغات المدعومة
        $supportedLocales = ['ar', 'en'];

        // نقرأ Accept-Language
        $locale = $request->header('Accept-Language');

        // إذا اللغة مو مدعومة، نرجع للافتراضي
        if (! in_array($locale, $supportedLocales)) {
            $locale = config('app.locale'); // غالبًا 'en'
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
