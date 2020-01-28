<?php

namespace Modules\Collaborators\Providers;

use Modules\Core\Entities\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Core\Policies\UsersPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array Access policies
     */
    protected $policies = [
        User::class => UsersPolicy::class,
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
