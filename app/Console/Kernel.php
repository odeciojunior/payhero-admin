<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        date_default_timezone_set('America/Sao_Paulo');

        $schedule->command('send:sms')->dailyAt('10:00');
        $schedule->command('verify:transfers')->dailyAt('03:00');
        //$schedule->command('verify:abandonedcarts')->hourly();

        $schedule->command('verify:pendingdomains')->hourly();

        //boletos
//        $schedule->command('verify:boletoexpired')->dailyAt('12:00');
//        $schedule->command('verify:boletoexpired2')->dailyAt('12:00');
//        $schedule->command('verify:boletoexpired3')->dailyAt('12:00');
//        $schedule->command('verify:boletoexpired4')->dailyAt('12:00');
//        $schedule->command('verify:boletoexpiring')->dailyAt('12:00');
//        $schedule->command('verify:boletowaitingpayment')->dailyAt('12:00');

        //carrinhos abandonados
//        $schedule->command('verify:abandonedcarts')->everyFifteenMinutes();

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
