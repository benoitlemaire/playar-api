<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use Closure;
use Illuminate\Http\Request;

class IsSuperAdmin
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
       if (auth()->user()->hasRole('superadmin')) {
           return $next($request);
       }

        return response()->json(['error' => 'Unauthenticated.'], 401);
    }
}
