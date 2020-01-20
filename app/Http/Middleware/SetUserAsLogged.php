<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class SetUserAsLogged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Redis::set('user-logged-'.auth()->user()->id, 'true', 'EX', 300);  // key expire in 300 seconds

        return $next($request);
    }

}
