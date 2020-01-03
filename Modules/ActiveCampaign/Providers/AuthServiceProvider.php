<?php

namespace Modules\ActiveCampaign\Providers;

use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\ActiveCampaign\Policies\ActiveCampaignPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array Access policies
     */
    protected $policies = [
        ActivecampaignIntegration::class => ActiveCampaignPolicy::class,
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
