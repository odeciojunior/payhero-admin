<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Route::macro(
            'toDemoAccount',
            function (Request $request, string $routeAction) {
                
                $route = tap($this->routes->getByAction($routeAction))->bind($request);
        
                $this->current = $route;
        
                return $this->runRoute($request, $this->current);

            }
        );
    }
}
