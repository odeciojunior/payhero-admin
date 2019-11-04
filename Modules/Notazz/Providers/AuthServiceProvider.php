<?php

namespace Modules\Notazz\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Notazz\Policies\NotazzPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $policies = [
        NotazzIntegration::class => NotazzPolicy::class,
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
