<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Core\Events\UserRegistrationEvent;
use Modules\Core\Events\BilletPaidEvent;
use Modules\Core\Listeners\BilletPaidSendEmailListener;
use Modules\Core\Listeners\UserRegistrationListener;
use Modules\Core\Listeners\BilletPaidActiveCampaignListener;
use Modules\Core\Listeners\BilletPaidHotZappListener;
// use Modules\Core\Listeners\BilletPaidDigitalManagerListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     * @var array
     */
    protected $listen = [
        UserRegistrationEvent::class                          => [
            UserRegistrationListener::class,
        ],
        BilletPaidEvent::class                                => [
            BilletPaidActiveCampaignListener::class,
            BilletPaidHotZappListener::class,
            BilletPaidSendEmailListener::class,
            // BilletPaidDigitalManagerListener::class,
        ],
        'Modules\Core\Events\ShopifyIntegrationEvent'         => [
            'Modules\Core\Listeners\ImportShopifyStoreListener',
        ],
        'Modules\Core\Events\ShopifyIntegrationReadyEvent'    => [
            'Modules\Core\Listeners\NotifyUserShopifyIntegrationReadyListener',
            'Modules\Core\Listeners\NotifyUserShopifyIntegrationStoreListener',
        ],
        'Modules\Core\Events\DomainApprovedEvent'             => [
            'Modules\Core\Listeners\DomainApprovedPusherNotifyUserListener',
            'Modules\Core\Listeners\DomainApprovedNotifyUserListener',
            'Modules\Core\Listeners\DomainApprovedEmailNotifyUserListener',
        ],
        'Modules\Core\Events\BoletoPaidEvent'                 => [
            'Modules\Core\Listeners\BoletoPaidPusherNotifyUser',
            'Modules\Core\Listeners\BoletoPaidNotifyUser',
            'Modules\Core\Listeners\BoletoPaidEmailNotifyUser',
        ],
        'Modules\Core\Events\TrackingsImportedEvent'        => [
            'Modules\Core\Listeners\NotifyTrackingsImportedListener',
        ],
        'Modules\Core\Events\TrackingsExportedEvent'        => [
            'Modules\Core\Listeners\NotifyTrackingsExportedListener',
        ],
        'Modules\Core\Events\TrackingCodeUpdatedEvent'        => [
            'Modules\Core\Listeners\TrackingCodeUpdatedSendEmailClientListener',
        ],
        'Modules\Core\Events\ResetPasswordEvent'              => [
            'Modules\Core\Listeners\ResetPasswordSendEmailListener',
        ],
        'Modules\Core\Events\ReleasedBalanceEvent'            => [
            'Modules\Core\Listeners\ReleasedBalanceNotifyUserListener',
        ],
        'Modules\Core\Events\SaleApprovedEvent'               => [
            //            'Modules\Core\Listeners\NotifyUsersApprovedSaleListener',
            //            'Modules\Core\Listeners\PusherNotificationApprovedSaleListener',
            'Modules\Core\Listeners\SetApprovedShopifyOrderListener',
            //            'Modules\Core\Listeners\HotZappCardApprovedSaleListener',
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\Shopify\\ShopifyExtendSocialite@handle',
        ],
        'Modules\Core\Events\WithdrawalRequestEvent'          => [
            'Modules\Core\Listeners\WithdrawalRequestSendEmailListener',
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
