<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SwaggerNoCSRF
{

    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->remove('X-CSRF-TOKEN');
        $request->headers->remove('x-csrf-token');
        $request->headers->remove('X-Csrf-Token');

        config(['session.driver' => 'array']);

        $response = $next($request);

        $response->headers->removeCookie('XSRF-TOKEN');
        $response->headers->removeCookie('laravel_session');

        return $response;
    }
}
