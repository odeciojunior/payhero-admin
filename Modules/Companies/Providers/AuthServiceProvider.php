<?php

namespace Modules\Companies\Providers;

use Modules\Core\Entities\Company;
use Modules\Companies\Policies\CompanyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array Politicas
     */
    protected $policies = [
        Company::class => CompanyPolicy::class,
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
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
