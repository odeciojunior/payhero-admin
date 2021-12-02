<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 * @package App\Console
 */
class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule)
    {
        setlocale(LC_ALL, 'pt_BR');

        $schedule->command('antifraudpostbacks:process')->withoutOverlapping()->everyMinute();

        $schedule->command('gatewaypostbacks:process')->withoutOverlapping()->everyFiveMinutes();

        $schedule->command('check:systems')->everyTenMinutes();

        $schedule->command('check:underattack')->everyThirtyMinutes();

        $schedule->command('withdrawals:release-get-faster')->withoutOverlapping()->everyThirtyMinutes();

        $schedule->command('updateTransactionsReleaseDate')->hourly();

        $schedule->command('whiteblacklist:verifyexpires')->dailyAt('00:00');

        $schedule->command('check:has-valid-tracking')->weekly()->thursdays()->at('01:00');

        $schedule->command('getnet:check-refunded')->dailyAt('03:15');

        $schedule->command('getnet:update-confirm-date-debt-pending')->dailyAt('04:00');

        $schedule->command('under-attack:update-card-declined')->dailyAt('05:00');

        $schedule->command('getnet:get-all-statement-chargebacks')->dailyAt('07:00');

        $schedule->command('getnet:check-withdrawals-liquidated')->dailyAt('10:00');
        $schedule->command('getnet:check-withdrawals-liquidated')->dailyAt('13:00');
        $schedule->command('getnet:check-withdrawals-liquidated')->dailyAt('17:00');
        $schedule->command('getnet:check-withdrawals-liquidated')->dailyAt('21:00');
        $schedule->command('getnet:check-withdrawals-liquidated')->dailyAt('23:30');

        $schedule->command('getnet:check-withdrawals-released')->dailyAt('09:00');
        $schedule->command('getnet:check-withdrawals-released')->dailyAt('12:00');
        $schedule->command('getnet:check-withdrawals-released')->dailyAt('16:00');

        $schedule->command('getnet:import-sale-contestations-txt-format')->dailyAt('16:00');

        $schedule->command('getnet:import-sale-contestations')->dailyAt('17:00');

        $schedule->command('getnet:check-withdrawals-released')->dailyAt('22:30');

        $schedule->command('verify:promotional-tax')->dailyAt('23:30');


        /** sirius */
        // snapshot for horizon metrics
        $schedule->command('horizon:snapshot')->everyFifteenMinutes();

        // update pending domains automaticaly
        $schedule->command('verify:pendingdomains')->hourly();

        // generate all sale approved invoices
        $schedule->command('generate:notazzinvoicessalesapproved')->everyThirtyMinutes();

        // verify pending notazz invoices
        $schedule->command('verify:pendingnotazzinvoices')->everyThirtyMinutes()->withoutOverlapping();

        // pega as ultimas quotacoes das moedas
        $schedule->command('update:currencyquotation')->dailyAt('14:00');

        // notify user to paid boletos
        $schedule->command('verify:boletopaid')->dailyAt('10:30');

        // boletos
        $schedule->command('verify:boletowaitingpayment')->dailyAt('10:00');
        $schedule->command('verify:boleto2')->dailyAt('11:15');
        $schedule->command('verify:boletoexpiring')->dailyAt('11:00');

        // abandoned carts
        $schedule->command('verify:abandonedcarts')->everyFifteenMinutes()->withoutOverlapping();

        $schedule->command('verify:abandonedcarts2')->dailyAt('12:00');

        // Alterar status do boletos de pendente para cancelado
        $schedule->command('change:boletopendingtocanceled')->dailyAt('06:30');

        $schedule->command('command:UpdateListsFoxActiveCampaign')->cron('0 */12 * * *');

        //verify last domains on sendgrid
        $schedule->command('command:validateLastDomains')->dailyAt('04:00');

        //Reorder shopify
        $schedule->command('command:ShopifyReorderSales')->dailyAt('03:00');

        //Reorder woocommerce
        $schedule->command('command:WoocommerceReorderSales')->dailyAt('03:45');

        //Retry woocommerce requests
        $schedule->command('command:WoocommerceRetryFailedRequests')->dailyAt('04:15');

        //checks the trackings that have been recognized by the carrier but has no movement yet
        $schedule->command('verify:trackingWithoutInfo')->dailyAt('15:00');

        //checks companies update on getnet
        $schedule->command('command:checkUpdateCompanyGetnet')->everyFourHours();

        //check invites expired
        $schedule->command('verify:inviteexpired')->dailyAt('01:00');

        $schedule->command('check:menv-tracking')->dailyAt('17:00');

        //Remove temporary files in regiter
        $schedule->command('command:deleteTemporaryFiles')->dailyAt('04:00');

        $schedule->command('available-balance:update')->dailyAt('06:15');

        $schedule->command('redis:update-sale-tracking')->hourly();

        $schedule->command('check:automatic-withdrawals')->dailyAt('03:10');

        /** Account health */
        $schedule->command('account-health:user:update-average-response-time')->dailyAt('02:00');
        $schedule->command('command:update-user-level')->dailyAt('11:15');
        $schedule->command('account-health:update')->dailyAt('09:00');
        $schedule->command('account-health:update')->dailyAt('22:00');

        /** Benefits: needs to be run after account-health:updates  */
        $schedule->command('user:benefits:update')->dailyAt('09:30');
        $schedule->command('user:benefits:update')->dailyAt('22:30');

        /** Tasks */
        $schedule->command('tasks:check-completed-sales-tasks')->dailyAt('00:30');
        $schedule->command('tasks:check-completed-sales-tasks')->dailyAt('06:30');
        $schedule->command('tasks:check-completed-sales-tasks')->dailyAt('10:30');
        $schedule->command('tasks:check-completed-sales-tasks')->dailyAt('14:30');
        $schedule->command('tasks:check-completed-sales-tasks')->dailyAt('18:30');
        $schedule->command('tasks:check-completed-sales-tasks')->dailyAt('22:30');

        /** Achievements */
        $schedule->command('achievements:update')->dailyAt('09:00');
        $schedule->command('achievements:update')->dailyAt('21:00');

        /** Pix Canceled */
        $schedule->command('change:pix-to-canceled')->everyMinute()->withoutOverlapping();

        /** Check GatewayTax invitations Diogo */
        $schedule->command('check:GatewayTaxCompanyAfterMonth')->dailyAt('06:30');

        $schedule->command('check:sales-refunded')->weeklyOn(1, '23:00');

        /** Libera o dinheiro da azx */
        $schedule->command('getnet:check-withdrawals-released-cloudfox')->dailyAt('22:00');
        /** Confirma a transferencia do dinheiro da azx */
        $schedule->command('getnet:check-withdrawals-liquidated-cloudfox')->dailyAt('22:30');

        /** Antecipações Asaas */
        $schedule->command('anticipations:asaas')->dailyAt('4:00');
        $schedule->command('anticipations:asaas-pending')->dailyAt('14:00');
        $schedule->command('anticipations:asaas-pending')->dailyAt('16:00');

        /** Sincronizar códigos de rastreio com WooCommerce */
        $schedule->command('woocommerce:check-tracking-codes')->weekly()->sundays()->at('07:00');
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
