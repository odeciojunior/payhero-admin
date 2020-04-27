<?php

namespace App\Providers;

use Modules\Core\Events\BilletPaidEvent;
use Modules\Core\Events\BilletRefundedEvent;
use Modules\Core\Events\SaleRefundedEvent;
use Modules\Core\Events\SaleRefundedPartialEvent;
use Modules\Core\Events\BilletExpiredEvent;
use Modules\Core\Listeners\BilletPaidHotZappListener;
use Modules\Core\Listeners\BilletPaidSendEmailListener;
use Modules\Core\Listeners\BilletPaidWhatsapp2Listener;
use Modules\Core\Listeners\BilletRefundedSendEmailListener;
use Modules\Core\Listeners\SaleRefundedSendEmailListener;
use Modules\Core\Listeners\SaleRefundedWhatsapp2Listener;
use Modules\Core\Listeners\BilletExpiredWhatsapp2Listener;
use Modules\Core\Listeners\BilletPaidActiveCampaignListener;
use Modules\Core\Listeners\SaleRefundedPartialSendEmailListener;
use Modules\Core\Listeners\BilletPaidHotsacListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     * @var array
     */
    protected $listen = [
        BilletPaidEvent::class => [
            BilletPaidActiveCampaignListener::class,
            BilletPaidHotZappListener::class,
            BilletPaidSendEmailListener::class,
            BilletPaidWhatsapp2Listener::class,
            BilletPaidHotsacListener::class,
        ],
        BilletExpiredEvent::class => [
            BilletExpiredWhatsapp2Listener::class,
        ],
        SaleRefundedEvent::class => [
            SaleRefundedWhatsapp2Listener::class,
            SaleRefundedSendEmailListener::class,
        ],
        SaleRefundedPartialEvent::class => [
            SaleRefundedPartialSendEmailListener::class,
        ],
        BilletRefundedEvent::class => [
            BilletRefundedSendEmailListener::class,
        ],
        'Modules\Core\Events\ShopifyIntegrationEvent' => [
            'Modules\Core\Listeners\ImportShopifyStoreListener',
        ],
        'Modules\Core\Events\ShopifyIntegrationReadyEvent' => [
            'Modules\Core\Listeners\NotifyUserShopifyIntegrationReadyListener',
            'Modules\Core\Listeners\NotifyUserShopifyIntegrationStoreListener',
        ],
        'Modules\Core\Events\DomainApprovedEvent' => [
            'Modules\Core\Listeners\DomainApprovedPusherNotifyUserListener',
            'Modules\Core\Listeners\DomainApprovedNotifyUserListener',
            'Modules\Core\Listeners\DomainApprovedEmailNotifyUserListener',
        ],
        'Modules\Core\Events\BoletoPaidEvent' => [
            'Modules\Core\Listeners\BoletoPaidPusherNotifyUser',
            'Modules\Core\Listeners\BoletoPaidNotifyUser',
            'Modules\Core\Listeners\BoletoPaidEmailNotifyUser',
        ],
        'Modules\Core\Events\TrackingsImportedEvent' => [
            'Modules\Core\Listeners\NotifyTrackingsImportedListener',
        ],
        'Modules\Core\Events\SalesExportedEvent' => [
            'Modules\Core\Listeners\NotifySalesExportedListener',
        ],
        'Modules\Core\Events\ExtractExportedEvent' => [
            'Modules\Core\Listeners\NotifyExtractExportedListener',
        ],
        'Modules\Core\Events\TrackingsExportedEvent' => [
            'Modules\Core\Listeners\NotifyTrackingsExportedListener',
        ],
        'Modules\Core\Events\TrackingCodeUpdatedEvent' => [
            'Modules\Core\Listeners\TrackingCodeUpdatedSendEmailClientListener',
            'Modules\Core\Listeners\TrackingCodeUpdatedActiveCampaignListener',
        ],
        'Modules\Core\Events\ResetPasswordEvent' => [
            'Modules\Core\Listeners\ResetPasswordSendEmailListener',
        ],
        'Modules\Core\Events\ReleasedBalanceEvent' => [
            'Modules\Core\Listeners\ReleasedBalanceNotifyUserListener',
        ],
        'Modules\Core\Events\SaleApprovedEvent' => [
            'Modules\Core\Listeners\SetApprovedShopifyOrderListener',
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\Shopify\\ShopifyExtendSocialite@handle',
        ],
        'Modules\Core\Events\WithdrawalRequestEvent' => [
            'Modules\Core\Listeners\WithdrawalRequestSendEmailListener',
        ],
        'Modules\Core\Events\SendEmailEvent' => [
            'Modules\Core\Listeners\SendEmailListener',
        ],
        'Modules\Core\Events\SendSmsEvent' => [
            'Modules\Core\Listeners\SendSmsListener',
        ],
        'Modules\Core\Events\TicketMessageEvent' => [
            'Modules\Core\Listeners\TicketMessageSendEmailListener',
        ],
        'Modules\Core\Events\AffiliateRequestEvent'           => [
            'Modules\Core\Listeners\AffiliateRequestSendEmailListener',
        ],
        'Modules\Core\Events\AffiliateEvent'                  => [
            'Modules\Core\Listeners\AffiliateSendEmailListener',
        ],
        'Modules\Core\Events\EvaluateAffiliateRequestEvent'   => [
            'Modules\Core\Listeners\EvaluateAffiliateRequestSendEmailListener',
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
