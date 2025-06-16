<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || is_null($user->verified_at)) {
            return response()->json([
                'message' => 'You must verify your phone number to perform this action',
                'Send verification code' => route('send.code'),
            ], 403);
        }
        return $next($request);
    }
}
