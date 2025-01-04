<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\IntegrationOrderCancelListener;
use App\Observers\TransactionObserver;
use App\Observers\TransferObserver;
use App\Observers\WithdrawalObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Events\AffiliateEvent;
use Modules\Core\Events\AffiliateRequestEvent;
use Modules\Core\Events\BilletExpiredEvent;
use Modules\Core\Events\CheckSaleHasValidTrackingEvent;
use Modules\Core\Events\CheckTransactionReleasedEvent;
use Modules\Core\Events\DomainApprovedEvent;
use Modules\Core\Events\EvaluateAffiliateRequestEvent;
use Modules\Core\Events\ExtractExportedEvent;
use Modules\Core\Events\FinancesExportedEvent;
use Modules\Core\Events\ImportNuvemshopProductsEvent;
use Modules\Core\Events\ManualRefundEvent;
use Modules\Core\Events\NewChargebackEvent;
use Modules\Core\Events\NotifyUserAchievementEvent;
use Modules\Core\Events\NotifyUserLevelEvent;
use Modules\Core\Events\PixExpiredEvent;
use Modules\Core\Events\ReleasedBalanceEvent;
use Modules\Core\Events\ReportanaTrackingEvent;
use Modules\Core\Events\ResetPasswordEvent;
use Modules\Core\Events\Sac\NotifyTicketClosedEvent;
use Modules\Core\Events\Sac\NotifyTicketMediationEvent;
use Modules\Core\Events\Sac\NotifyTicketOpenEvent;
use Modules\Core\Events\Sac\TicketMessageEvent;
use Modules\Core\Events\SaleApprovedEvent;
use Modules\Core\Events\SaleRefundedEvent;
use Modules\Core\Events\SalesExportedEvent;
use Modules\Core\Events\SendEmailEvent;
use Modules\Core\Events\SendEmailPendingDocumentEvent;
use Modules\Core\Events\SendSmsEvent;
use Modules\Core\Events\ShopifyIntegrationReadyEvent;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Events\TrackingsExportedEvent;
use Modules\Core\Events\TrackingsImportedEvent;
use Modules\Core\Events\UpdateCompanyGetnetEvent;
use Modules\Core\Events\UserRegisteredEvent;
use Modules\Core\Events\UserRegistrationFinishedEvent;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Events\WithdrawalsExportedEvent;
use Modules\Core\Listeners\AffiliateRequestSendEmailListener;
use Modules\Core\Listeners\AffiliateSendEmailListener;
use Modules\Core\Listeners\BilletExpiredWhatsapp2Listener;
use Modules\Core\Listeners\CheckSaleHasValidTrackingListener;
use Modules\Core\Listeners\CheckTransactionReleasedListener;
use Modules\Core\Listeners\CreateChargebackDebitListener;
use Modules\Core\Listeners\DomainApprovedEmailNotifyUserListener;
use Modules\Core\Listeners\DomainApprovedNotifyUserListener;
use Modules\Core\Listeners\DomainApprovedPusherNotifyUserListener;
use Modules\Core\Listeners\EvaluateAffiliateRequestSendEmailListener;
use Modules\Core\Listeners\HotBilletPixExpiredListener;
use Modules\Core\Listeners\ImportNuvemshopProductsListener;
use Modules\Core\Listeners\ManualRefundedSendEmailListener;
use Modules\Core\Listeners\NotificacoesInteligentesPixExpiredListener;
use Modules\Core\Listeners\NotifyAntifraudChargebackListener;
use Modules\Core\Listeners\NotifyExtractExportedListener;
use Modules\Core\Listeners\NotifyFinancesExportedListener;
use Modules\Core\Listeners\NotifySalesExportedListener;
use Modules\Core\Listeners\NotifyTrackingsExportedListener;
use Modules\Core\Listeners\NotifyTrackingsImportedListener;
use Modules\Core\Listeners\NotifyUserAchievementSendEmailListener;
use Modules\Core\Listeners\NotifyUserLevelSendEmailListener;
use Modules\Core\Listeners\NotifyUserShopifyIntegrationReadyListener;
use Modules\Core\Listeners\NotifyUserShopifyIntegrationStoreListener;
use Modules\Core\Listeners\NotifyWithdrawalsExportedListener;
use Modules\Core\Listeners\PixExpiredSendEmailListener;
use Modules\Core\Listeners\PixExpiredUnicodropListener;
use Modules\Core\Listeners\ReleasedBalanceNotifyUserListener;
use Modules\Core\Listeners\ReportanaSaleListener;
use Modules\Core\Listeners\ReportanaSaleRecoveryListener;
use Modules\Core\Listeners\ResetPasswordSendEmailListener;
use Modules\Core\Listeners\Sac\NotifyTicketClosedListener;
use Modules\Core\Listeners\Sac\NotifyTicketMediationListener;
use Modules\Core\Listeners\Sac\NotifyTicketOpenListener;
use Modules\Core\Listeners\Sac\TicketMessageSendEmailListener;
use Modules\Core\Listeners\Sak\SakPixExpiredListener;
use Modules\Core\Listeners\SaleRefundedSendEmailListener;
use Modules\Core\Listeners\SaleRefundedWhatsapp2Listener;
use Modules\Core\Listeners\SendChargebackNotificationsListener;
use Modules\Core\Listeners\SendEmailListener;
use Modules\Core\Listeners\SendEmailPedingDocumentoListener;
use Modules\Core\Listeners\SendEmailRegisteredListener;
use Modules\Core\Listeners\SendSmsListener;
use Modules\Core\Listeners\SetApprovedShopifyOrderListener;
use Modules\Core\Listeners\TrackingCodeUpdatedActiveCampaignListener;
use Modules\Core\Listeners\TrackingCodeUpdatedSendEmailClientListener;
use Modules\Core\Listeners\UpdateCompanyGetnetSendEmailListener;
use Modules\Core\Listeners\UpdateSaleChargebackListener;
use Modules\Core\Listeners\UserDocumentBureauValidationListener;
use Modules\Core\Listeners\WithdrawalRequestSendEmailListener;
use Modules\Webhooks\Listeners\WebhookSaleListener;

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
        BilletExpiredEvent::class => [
            BilletExpiredWhatsapp2Listener::class,
            IntegrationOrderCancelListener::class,
            ReportanaSaleListener::class,
            ReportanaSaleRecoveryListener::class,
        ],
        SaleRefundedEvent::class => [
            SaleRefundedWhatsapp2Listener::class,
            SaleRefundedSendEmailListener::class,
            IntegrationOrderCancelListener::class,
            WebhookSaleListener::class,
        ],
        ManualRefundEvent::class => [
            ManualRefundedSendEmailListener::class,
            IntegrationOrderCancelListener::class,
            WebhookSaleListener::class,
        ],
        ShopifyIntegrationReadyEvent::class => [
            NotifyUserShopifyIntegrationReadyListener::class,
            NotifyUserShopifyIntegrationStoreListener::class,
        ],
        DomainApprovedEvent::class => [
            DomainApprovedPusherNotifyUserListener::class,
            DomainApprovedNotifyUserListener::class,
            DomainApprovedEmailNotifyUserListener::class,
        ],
        TrackingsImportedEvent::class => [NotifyTrackingsImportedListener::class],
        SalesExportedEvent::class => [NotifySalesExportedListener::class],
        ExtractExportedEvent::class => [NotifyExtractExportedListener::class],
        TrackingsExportedEvent::class => [NotifyTrackingsExportedListener::class],
        TrackingCodeUpdatedEvent::class => [
            TrackingCodeUpdatedSendEmailClientListener::class,
            TrackingCodeUpdatedActiveCampaignListener::class,
        ],
        CheckSaleHasValidTrackingEvent::class => [CheckSaleHasValidTrackingListener::class],
        ResetPasswordEvent::class => [ResetPasswordSendEmailListener::class],
        ReleasedBalanceEvent::class => [ReleasedBalanceNotifyUserListener::class],
        SaleApprovedEvent::class => [SetApprovedShopifyOrderListener::class],
        WithdrawalRequestEvent::class => [WithdrawalRequestSendEmailListener::class],
        SendEmailEvent::class => [SendEmailListener::class],
        SendEmailPendingDocumentEvent::class => [SendEmailPedingDocumentoListener::class],
        SendSmsEvent::class => [SendSmsListener::class],
        TicketMessageEvent::class => [TicketMessageSendEmailListener::class],
        NotifyTicketMediationEvent::class => [NotifyTicketMediationListener::class],
        NotifyTicketOpenEvent::class => [NotifyTicketOpenListener::class],
        NotifyTicketClosedEvent::class => [NotifyTicketClosedListener::class],
        AffiliateRequestEvent::class => [AffiliateRequestSendEmailListener::class],
        AffiliateEvent::class => [AffiliateSendEmailListener::class],
        EvaluateAffiliateRequestEvent::class => [EvaluateAffiliateRequestSendEmailListener::class],
        UserRegisteredEvent::class => [SendEmailRegisteredListener::class],
        UserRegistrationFinishedEvent::class => [UserDocumentBureauValidationListener::class],
        UpdateCompanyGetnetEvent::class => [UpdateCompanyGetnetSendEmailListener::class],
        FinancesExportedEvent::class => [NotifyFinancesExportedListener::class],
        WithdrawalsExportedEvent::class => [NotifyWithdrawalsExportedListener::class],
        NotifyUserLevelEvent::class => [NotifyUserLevelSendEmailListener::class],
        NotifyUserAchievementEvent::class => [NotifyUserAchievementSendEmailListener::class],
        PixExpiredEvent::class => [
            PixExpiredSendEmailListener::class,
            HotBilletPixExpiredListener::class,
            NotificacoesInteligentesPixExpiredListener::class,
            SakPixExpiredListener::class,
            PixExpiredUnicodropListener::class,
            IntegrationOrderCancelListener::class,
            ReportanaSaleListener::class,
            ReportanaSaleRecoveryListener::class,
            WebhookSaleListener::class,
        ],
        CheckTransactionReleasedEvent::class => [CheckTransactionReleasedListener::class],
        NewChargebackEvent::class => [
            UpdateSaleChargebackListener::class,
            CreateChargebackDebitListener::class,
            SendChargebackNotificationsListener::class,
            NotifyAntifraudChargebackListener::class,
        ],
        ReportanaTrackingEvent::class => [ReportanaSaleListener::class],
        ImportNuvemshopProductsEvent::class => [ImportNuvemshopProductsListener::class],
    ];

    /**
     * Register any events for your application.
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        Transaction::observe(TransactionObserver::class);
        Transfer::observe(TransferObserver::class);
        Withdrawal::observe(WithdrawalObserver::class);
    }
}
