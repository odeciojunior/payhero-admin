<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     * @var array
     */
    protected $listen = [
        \Modules\Core\Events\BilletExpiredEvent::class => [
            \Modules\Core\Listeners\BilletExpiredWhatsapp2Listener::class,
        ],
        \Modules\Core\Events\SaleRefundedEvent::class => [
            \Modules\Core\Listeners\SaleRefundedWhatsapp2Listener::class,
            \Modules\Core\Listeners\SaleRefundedSendEmailListener::class,
        ],
        \Modules\Core\Events\SaleRefundedPartialEvent::class => [
            \Modules\Core\Listeners\SaleRefundedPartialSendEmailListener::class,
        ],
        \Modules\Core\Events\BilletRefundedEvent::class => [
            \Modules\Core\Listeners\BilletRefundedSendEmailListener::class,
        ],
        \Modules\Core\Events\ShopifyIntegrationEvent::class => [
            \Modules\Core\Listeners\ImportShopifyStoreListener::class,
        ],
        \Modules\Core\Events\ShopifyIntegrationReadyEvent::class => [
            \Modules\Core\Listeners\NotifyUserShopifyIntegrationReadyListener::class,
            \Modules\Core\Listeners\NotifyUserShopifyIntegrationStoreListener::class,
        ],
        \Modules\Core\Events\DomainApprovedEvent::class => [
            \Modules\Core\Listeners\DomainApprovedPusherNotifyUserListener::class,
            \Modules\Core\Listeners\DomainApprovedNotifyUserListener::class,
            \Modules\Core\Listeners\DomainApprovedEmailNotifyUserListener::class,
        ],
        \Modules\Core\Events\BoletoPaidEvent::class => [
            \Modules\Core\Listeners\BoletoPaidPusherNotifyUser::class,
            \Modules\Core\Listeners\BoletoPaidNotifyUser::class,
            \Modules\Core\Listeners\BoletoPaidEmailNotifyUser::class,
        ],
        \Modules\Core\Events\TrackingsImportedEvent::class => [
            \Modules\Core\Listeners\NotifyTrackingsImportedListener::class,
        ],
        \Modules\Core\Events\SalesExportedEvent::class => [
            \Modules\Core\Listeners\NotifySalesExportedListener::class,
        ],
        \Modules\Core\Events\ExtractExportedEvent::class => [
            \Modules\Core\Listeners\NotifyExtractExportedListener::class,
        ],
        \Modules\Core\Events\TrackingsExportedEvent::class => [
            \Modules\Core\Listeners\NotifyTrackingsExportedListener::class,
        ],
        \Modules\Core\Events\TrackingCodeUpdatedEvent::class => [
            \Modules\Core\Listeners\TrackingCodeUpdatedSendEmailClientListener::class,
            \Modules\Core\Listeners\TrackingCodeUpdatedActiveCampaignListener::class,
        ],
        \Modules\Core\Events\CheckSaleHasValidTrackingEvent::class => [
            \Modules\Core\Listeners\CheckSaleHasValidTrackingListener::class
        ],
        \Modules\Core\Events\ResetPasswordEvent::class => [
            \Modules\Core\Listeners\ResetPasswordSendEmailListener::class,
        ],
        \Modules\Core\Events\ReleasedBalanceEvent::class => [
            \Modules\Core\Listeners\ReleasedBalanceNotifyUserListener::class,
        ],
        \Modules\Core\Events\SaleApprovedEvent::class => [
            \Modules\Core\Listeners\SetApprovedShopifyOrderListener::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\Shopify\\ShopifyExtendSocialite@handle',
        ],
        \Modules\Core\Events\WithdrawalRequestEvent::class => [
            \Modules\Core\Listeners\WithdrawalRequestSendEmailListener::class,
        ],
        \Modules\Core\Events\SendEmailEvent::class => [
            \Modules\Core\Listeners\SendEmailListener::class,
        ],
        \Modules\Core\Events\SendSmsEvent::class => [
            \Modules\Core\Listeners\SendSmsListener::class,
        ],
        \Modules\Core\Events\TicketMessageEvent::class => [
            \Modules\Core\Listeners\TicketMessageSendEmailListener::class,
        ],
        \Modules\Core\Events\AffiliateRequestEvent::class => [
            \Modules\Core\Listeners\AffiliateRequestSendEmailListener::class,
        ],
        \Modules\Core\Events\AffiliateEvent::class => [
            \Modules\Core\Listeners\AffiliateSendEmailListener::class,
        ],
        \Modules\Core\Events\EvaluateAffiliateRequestEvent::class => [
            \Modules\Core\Listeners\EvaluateAffiliateRequestSendEmailListener::class,
        ],
        \Modules\Core\Events\UserRegisteredEvent::class => [
            \Modules\Core\Listeners\SendEmailRegisteredListener::class,
        ],
        \Modules\Core\Events\UpdateCompanyGetnetEvent::class => [
            \Modules\Core\Listeners\UpdateCompanyGetnetSendEmailListener::class,
        ],
        \Modules\Core\Events\FinancesExportedEvent::class => [
            \Modules\Core\Listeners\NotifyFinancesExportedListener::class,
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
