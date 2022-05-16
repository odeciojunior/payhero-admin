<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
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
        Config::set('database.default', 'mysql');
        \Log::info($request->path());
        if(str_contains($request->path(),'api/') && !str_contains($request->path(),'api/core/company-default'))
        {
            $user = Auth::user();

            if($user->company_default == 1)
            {
                Config::set('database.default', 'demo');
                
                $routeAction = $request->route()->getAction()['controller'];                
                $routeAction = str_replace(
                    ['Controller@',explode("\\",$routeAction)['1'].'\\'],
                    ['DemoController@','DemoAccount\\'],
                    $routeAction
                );

                \Log::info($routeAction);

                return Route::toDemoAccount($request, $routeAction);                
            }
        }
        
        return $next($request);
    }
}
