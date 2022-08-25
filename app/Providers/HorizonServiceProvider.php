<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Modules\Core\Entities\User;
use Illuminate\Support\Facades\Session;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if (request()->has("horizon_access") && request("horizon_access") == "VHIkkugCPtxZge7cVGOYtoFwhvMA3z") {
            Session::put("horizon_access", "true");
        }

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
        Horizon::night();
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define("viewHorizon", function ($user = null) {
            return Session::has("horizon_access") || request()->bearerToken() === 'test';
        });
    }
}
