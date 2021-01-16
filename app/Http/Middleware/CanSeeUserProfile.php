<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanSeeUserProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        // Une entreprise qui a une offre qui elle même possède un utilisateur peut voir ce profile

        $user = auth()->user();

        if ($user->hasRole('superadmin') || $request->route('user')->id === $user->id) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthenticated.'], 401);

    }
}
