<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsValidated
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
        if (!$user->validated) {
            return response()->json(['error' => 'You need to be validated before applying to this offer.'], 403);
        }

        return $next($request);
    }
}
