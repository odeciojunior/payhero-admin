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
        'Modules\Core\Events\DomainApprovedEvent'          => [
            'Modules\Core\Listeners\DomainApprovedPusherNotifyUserListener',
            'Modules\Core\Listeners\DomainApprovedNotifyUserListener',
            'Modules\Core\Listeners\DomainApprovedEmailNotifyUserListener',
        ],
        'Modules\Core\Events\BoletoPaidEvent'              => [
            'Modules\Core\Listeners\BoletoPaidPusherNotifyUser',
            'Modules\Core\Listeners\BoletoPaidNotifyUser',
            'Modules\Core\Listeners\BoletoPaidEmailNotifyUser',
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
