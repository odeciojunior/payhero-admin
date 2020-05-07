<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class SetUserAsLogged
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!empty(auth()->user())) {
            Redis::set('user-logged-' . auth()->user()->id, 'true', 'EX', 300);  // key expire in 300 seconds
        }

        return $next($request);
    }
}
