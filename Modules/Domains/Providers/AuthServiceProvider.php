<?php

namespace Modules\Domains\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Core\Entities\Domain;
use Modules\Domains\Policies\DomainPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $policies = [
        Domain::class => DomainPolicy::class,
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
