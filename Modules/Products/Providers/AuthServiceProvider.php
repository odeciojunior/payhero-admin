<?php

namespace Modules\Products\Providers;

use Modules\Core\Entities\Product;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Products\Policies\ProductPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
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
