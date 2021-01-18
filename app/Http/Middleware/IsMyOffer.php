<?php

namespace App\Http\Middleware;

use App\Models\Offer;
use Closure;
use Illuminate\Http\Request;

class IsMyOffer
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
        $offer = $request->offer;

        if ($current_user->hasRole('superadmin') || $current_user->id === $offer->user_id) {
            return $next($request);
        }

        return response()->json(['error' => 'User does not have any of the necessary access rights.'], 403);
    }
}
