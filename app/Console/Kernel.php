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

        // transfer money from transactions for user companies
        $schedule->command('verify:transfers')->dailyAt('03:00');

        // update pending domains automaticaly
        $schedule->command('verify:pendingdomains')->hourly();

        //generate all sale approved invoices
        $schedule->command('generate:notazzinvoicessalesapproved')->everyFiveMinutes();
        //verify pending notazz invoices
        $schedule->command('verify:pendingnotazzinvoices')->everyMinute()->withoutOverlapping();

        //pega as ultimas quotacoes das moedas
        $schedule->command('update:currencyquotation')->dailyAt('12:00');

        // notify user to paid boletos
        $schedule->command('verify:boletopaid')->dailyAt('10:00');

        //boletos 
        $schedule->command('verify:boletowaitingpayment')->dailyAt('12:00');
        $schedule->command('verify:boleto2')->dailyAt('12:30');
        $schedule->command('verify:boletoexpiring')->dailyAt('13:00');

        // $schedule->command('verify:boletoexpired')->dailyAt('10:00');
        // $schedule->command('verify:boletoexpired3')->dailyAt('10:00');
        // $schedule->command('verify:boletoexpired4')->dailyAt('10:00');

        //abandoned carts
        $schedule->command('verify:abandonedcarts')->everyFifteenMinutes();
        $schedule->command('verify:abandonedcarts2')->dailyAt('11:30');

        //Alterar status do boletos de pendente para cancelado
        $schedule->command('change:boletopendingtocanceled')->dailyAt('08:30');
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
