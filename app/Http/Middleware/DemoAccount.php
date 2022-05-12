<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class DemoAccount
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
        
        if(str_contains($request->path(),'api/')){
            $route = str_replace('api/','',$request->path());
            return Route::toDemoAccount($request, str_replace('/','.',$route));
        }
        return $next($request);
    }
}
