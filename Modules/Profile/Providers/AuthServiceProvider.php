<?php

namespace Modules\Profile\Providers;

use App\Entities\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Profile\Policies\ProfilePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array Politicas
     */
    protected $policies = [
        User::class => ProfilePolicy::class,
    ];

    /**
     * Boot
     */
    public function boot()
    {
        $this->registerPolicies();
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
