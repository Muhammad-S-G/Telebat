<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected $available = ['ar', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('locale') ?: $request->header('Accept-Language') ?: $request->query('lang') ?: ($request->segment(1) ?? null);

        $locale = strtolower(substr($locale, 0, 2));

        if (!in_array($locale, $this->available)) {
            $locale = Config::get('app.fallback_locale', 'en');
        }

        App::setLocale($locale);
        return $next($request);
    }
}
