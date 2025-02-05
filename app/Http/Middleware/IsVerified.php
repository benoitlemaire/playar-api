<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user->verified) {
            return response()->json(['error' => 'User is not verified.'], 403);
        }

        return $next($request);
    }
}
