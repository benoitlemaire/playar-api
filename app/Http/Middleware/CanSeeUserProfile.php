<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CanSeeUserProfile
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

        if ($current_user->hasRole('superadmin') || $requested_user->id === $current_user->id) {
            return $next($request);
        }

        $ids = $current_user->offers->pluck('id');
        $applies = $requested_user->applies;

        foreach ($applies as $apply) {
            if (in_array($apply->id, $ids->toArray())) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'User does not have any of the necessary access rights.'], 403);
    }
}
