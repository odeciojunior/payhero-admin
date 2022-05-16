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
                
                $routeFragments = explode('/',$request->path());
                
                $routeName = 'demo.';
                foreach($routeFragments as $key=>$part)
                {
                    switch($key){
                        case 0: break;
                        case 1:
                            $routeName.= $part;
                            break;
                        case 2:
                            $routeName.='.'.$part; 
                            break;
                    }            
                }
                
                return Route::toDemoAccount($request, $routeName);
            }
        }
        
        return $next($request);
    }
}
