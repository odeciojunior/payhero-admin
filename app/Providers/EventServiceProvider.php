<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     * @var array
     */
    protected $listen = [
        'Modules\Core\Events\ShopifyIntegrationEvent'      => [
            'Modules\Core\Listeners\ImportShopifyStoreListener',
        ],
        'Modules\Core\Events\ShopifyIntegrationReadyEvent' => [
            'Modules\Core\Listeners\NotifyUserShopifyIntegrationReadyListener',
            'Modules\Core\Listeners\NotifyUserShopifyIntegrationStoreListener',
        ],
    ];

    /**
     * Register any events for your application.
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }
}
