<?php

namespace Modules\Companies\Providers;

use App\Entities\Company;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Companies\Policies\CompanyPolicy;

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
