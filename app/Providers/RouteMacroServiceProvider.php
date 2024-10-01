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

                $route = $this->routes->getByAction($routeAction);

                if (empty($route)) {
                    $this->current = tap($this->routes->getByName('demo.not_authorized'))->bind($request);
                } else {
                    $this->current = tap($route)->bind($request);

                    $parameters = $request->route()->parameters;
                    if (count($parameters) > 0) {
                        foreach ($parameters as $key => $param) {
                            $this->current->setParameter($key, $param);
                        }
                    }
                }

                return $this->runRoute($request, $this->current);

            }
        );
    }
}
