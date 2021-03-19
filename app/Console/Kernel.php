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
    /**
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        setlocale(LC_ALL, 'pt_BR');

        //command executa duas horas antes por causa do fuso horário

        // snapshot for horizon metrics
        $schedule->command('horizon:snapshot')->everyFifteenMinutes();

        // transfer money from transactions for user companies
        $schedule->command('verify:transfers')->dailyAt('05:00');

        // transfer money from transactions for user companies on getnet
        $schedule->command('verify:transfersgetnet')->dailyAt('11:00');
        $schedule->command('verify:transfersgetnet')->dailyAt('14:00');
        $schedule->command('verify:transfersgetnet')->dailyAt('16:00');
        $schedule->command('verify:transfersgetnet')->dailyAt('20:00');
        $schedule->command('verify:transfersgetnet')->dailyAt('23:30');

        // update pending domains automaticaly
        $schedule->command('verify:pendingdomains')->hourly();

        // generate all sale approved invoices
        $schedule->command('generate:notazzinvoicessalesapproved')->everyThirtyMinutes();

        // verify pending notazz invoices
        $schedule->command('verify:pendingnotazzinvoices')->everyThirtyMinutes()->withoutOverlapping();

        // pega as ultimas quotacoes das moedas
        $schedule->command('update:currencyquotation')->dailyAt('14:00');

        // notify user to paid boletos
        $schedule->command('verify:boletopaid')->dailyAt('12:00');

        // boletos
        $schedule->command('verify:boletowaitingpayment')->dailyAt('10:00');
        $schedule->command('verify:boleto2')->dailyAt('11:00');
        $schedule->command('verify:boletoexpiring')->dailyAt('11:30');

        // abandoned carts
        $schedule->command('verify:abandonedcarts')->everyFifteenMinutes()->withoutOverlapping();

        $schedule->command('verify:abandonedcarts2')->dailyAt('12:00');

        // Alterar status do boletos de pendente para cancelado
        $schedule->command('change:boletopendingtocanceled')->dailyAt('06:30');

        $schedule->command('command:UpdateListsFoxActiveCampaign')->cron('0 */12 * * *');

        // restart queues running on supervisor
        $schedule->command('queue:restart')->hourly();

        // verify redis status (ON - OFF)
        $schedule->command('verify:redis')->everyThirtyMinutes();

        //verify last domains on sendgrid
        $schedule->command('command:validateLastDomains')->dailyAt('04:00');

        //verify users that has antecipation enabled and does not have approved sales in the last three days

        //restart all shopify webhooks from shopify integrations
//        $schedule->command('restartShopifyWebhooks')->weekly();

        //Reorder shopify
        $schedule->command('command:ShopifyReorderSales')->dailyAt('03:00');

        //Reorder shopify hourly
        // $schedule->command('command:ShopifyReorderSalesHourly')->hourly();

        //checks the trackings that have been recognized by the carrier but has no movement yet
        $schedule->command('verify:trackingWithoutInfo')->dailyAt('15:00');

        //checks companies update on getnet
        $schedule->command('command:checkUpdateCompanyGetnet')->everyFourHours();

        //check invites expired
        $schedule->command('verify:inviteexpired')->dailyAt('01:00');

        //Remove temporary files in regiter
        $schedule->command('command:deleteTemporaryFiles')->dailyAt('04:00');

        $schedule->command('check:getnet-transactions')->dailyAt('06:00');

        $schedule->command('redis:update-sale-tracking')->hourly();

        $schedule->command('check:automatic-withdrawals')->dailyAt('03:10');

        $schedule->command('cloudfox:getnet-get-statement')->dailyAt('03:30');
        $schedule->command('cloudfox:getnet-get-statement')->dailyAt('09:30');
        $schedule->command('cloudfox:getnet-get-statement')->dailyAt('15:30');
        $schedule->command('cloudfox:getnet-get-statement')->dailyAt('21:30');
    }

    /**
     * Register the commands for the application.
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
