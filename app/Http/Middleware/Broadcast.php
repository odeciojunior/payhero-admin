<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class Broadcast
 * @package App\Http\Middleware
 */
class Broadcast
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $web = Auth::guard('web')->user();
        if ($web) {
            return response()->json(\Illuminate\Support\Facades\Broadcast::auth($request));
        }

        return response()->json('Unauthorized.', 500);
    }
}
