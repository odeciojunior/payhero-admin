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
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        // transfer money from transactions for user companies
        $schedule->command('verify:transfers')->dailyAt('05:00');

        // update pending domains automaticaly
        $schedule->command('verify:pendingdomains')->hourly();

        // generate all sale approved invoices
        $schedule->command('generate:notazzinvoicessalesapproved')->everyFiveMinutes();

        // verify pending notazz invoices
        $schedule->command('verify:pendingnotazzinvoices')->everyMinute()->withoutOverlapping();

        // pega as ultimas quotacoes das moedas
        $schedule->command('update:currencyquotation')->dailyAt('14:00');

        // notify user to paid boletos
        $schedule->command('verify:boletopaid')->dailyAt('12:00');

        // boletos
        $schedule->command('verify:boletowaitingpayment')->dailyAt('10:00');
        $schedule->command('verify:boleto2')->dailyAt('11:00');
        $schedule->command('verify:boletoexpiring')->dailyAt('11:30');

        // abandoned carts
        $schedule->command('verify:abandonedcarts')->everyFifteenMinutes();

        $schedule->command('verify:abandonedcarts2')->dailyAt('12:00');

        // Alterar status do boletos de pendente para cancelado
        $schedule->command('change:boletopendingtocanceled')->dailyAt('06:30');

        $schedule->command('command:UpdateListsFoxActiveCampaign')->cron('0 */12 * * *');

        // restart queues running on supervisor
        $schedule->command('queue:restart')->hourly();

        // verify checkout status (ON - OFF)
        $schedule->command('verify:checkout-status')->everyTenMinutes();

        // verify redis status (ON - OFF)
        $schedule->command('verify:redis')->everyThirtyMinutes();

        //verify last domains on sendgrid
        $schedule->command('command:validateLastDomains')->dailyAt('04:00');

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
