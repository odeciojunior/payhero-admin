<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Modules\Core\Entities\Company;

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

        $path = $request->path();
        if(auth()->guard('api')->check() && !str_contains($path,'api/core/company-default'))
        {
            $nextDemo = false;
            if( str_contains($path,'/mobile') ){
                $nextDemo = (request()->company_id??'') == Company::DEMO_HASH_ID;
            }else{
                $nextDemo = auth()->user()->company_default == Company::DEMO_ID;
            }

            if(!$nextDemo){
                return $next($request);
            }

            Config::set('database.default', 'demo');

            $routeAction = $request->route()->getAction()['controller'];
            $routeAction = str_replace(
                ['Controller@',explode("\\",$routeAction)['1'].'\\'],
                ['DemoController@','DemoAccount\\'],
                $routeAction
            );

            return Route::toDemoAccount($request, $routeAction);
        }

        return $next($request);
    }
}
