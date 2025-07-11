<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsCloudFoxAccount
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!str_contains(auth()->user()->email, "@azcend.com.br")) {
            abort(403);
        }

        return $next($request);
    }
}
