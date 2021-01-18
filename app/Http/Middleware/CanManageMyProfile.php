<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanManageMyProfile
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
        $current_user = auth()->user();
        $requested_user = $request->user;

        if ($current_user->hasRole('superadmin') || $current_user->id === $requested_user->id) {
            return $next($request);
        }

        return response()->json(['error' => 'User does not have any of the necessary access rights.'], 403);
    }
}
