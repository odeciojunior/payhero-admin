<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;

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
        setlocale(LC_ALL, "pt_BR");

        $schedule
            ->command(ScheduleCheckHeartbeatCommand::class)
            ->everyMinute()
            ->onOneServer();
        $schedule
            ->command("change:pix-to-canceled")
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer();

        $schedule
            ->command("horizon:snapshot")
            ->everyFiveMinutes()
            ->onOneServer();
        $schedule
            ->command("gatewaypostbacks:process")
            ->withoutOverlapping()
            ->everyFiveMinutes()
            ->onOneServer();

        $schedule
            ->command("paylab:proccess-postback")
            ->everyTenMinutes()
            ->onOneServer();

        $schedule
            ->command("demo:create-fake-checkout")
            ->everyTenMinutes()
            ->withoutOverlapping()
            ->onOneServer();
        $schedule
            ->command("withdrawals:release-get-faster")
            ->withoutOverlapping()
            ->everyMinute()
            ->onOneServer();
        $schedule
            ->command("available-balance:update")
            ->everyTenMinutes()            
            ->withoutOverlapping()
            ->onOneServer();
        $schedule
            ->command("verify:abandonedcarts")
            ->everyFifteenMinutes()
            ->withoutOverlapping()
            ->onOneServer();
        $schedule
            ->command("demo:create-fake-sale")
            ->withoutOverlapping()
            ->everyThirtyMinutes()
            ->onOneServer();

        $schedule
            ->command("verify:pendingdomains")
            ->hourly()
            ->onOneServer();
        $schedule
            ->command("verify:tickets")
            ->withoutOverlapping()
            ->hourly()
            ->onOneServer();
        $schedule
            ->command("verify:tickets-refunded")
            ->withoutOverlapping()
            ->hourly()
            ->onOneServer();

        $schedule
            ->command("iugu:create-seller-account")
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer();

        $schedule
            ->command("iugu:verification-account")
            ->everyTwoMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        $schedule
            ->command("malga:create-seller-account")
            ->everyTenMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        $schedule
            ->command("demo:create-fake-withdrawal")
            ->days([Schedule::MONDAY, Schedule::WEDNESDAY, Schedule::FRIDAY])
            ->at("05:20")
            ->onOneServer();
        $schedule
            ->command("woocommerce:check-tracking-codes")
            ->sundays()
            ->at("06:30")
            ->onOneServer();

        $schedule
            ->command("whiteblacklist:verifyexpires")
            ->dailyAt("00:00")
            ->onOneServer();
        $schedule
            ->command("check:has-valid-tracking")
            ->dailyAt("00:30")
            ->onOneServer();
        $schedule
            ->command("verify:inviteexpired")
            ->dailyAt("00:40")
            ->onOneServer();
        $schedule
            ->command("account-health:user:update-average-response-time")
            ->dailyAt("00:50")
            ->onOneServer();
        $schedule
            ->command("command:ShopifyReorderSales")
            ->dailyAt("01:30")
            ->onOneServer();
        $schedule
            ->command("command:WoocommerceReorderSales")
            ->dailyAt("02:45")
            ->onOneServer();
        $schedule
            ->command("check:automatic-withdrawals")
            ->dailyAt("03:10")
            ->onOneServer();
        $schedule
            ->command("command:deleteTemporaryFiles")
            ->dailyAt("03:20")
            ->onOneServer();
        $schedule
            ->command("command:validateLastDomains")
            ->dailyAt("04:00")
            ->onOneServer();
        $schedule
            ->command("command:WoocommerceRetryFailedRequests")
            ->dailyAt("04:15")
            ->onOneServer();
        $schedule
            ->command("under-attack:update-card-declined")
            ->dailyAt("05:00")
            ->onOneServer();
        $schedule
            ->command("change:boletopendingtocanceled")
            ->dailyAt("05:20")
            ->onOneServer();
        $schedule
            ->command("tasks:check-completed-sales-tasks")
            ->dailyAt("05:30")
            ->onOneServer();
        $schedule
            ->command("demo:abandoned-cart-checkout")
            ->dailyAt("05:35")
            ->onOneServer();
        $schedule
            ->command("demo:update-financial-fake-data")
            ->dailyAt("06:20")
            ->onOneServer();
        $schedule
            ->command("user:benefits:update")
            ->dailyAt("06:30")
            ->onOneServer();
        $schedule
            ->command("achievements:update")
            ->dailyAt("06:40")
            ->onOneServer();
        $schedule
            ->command("account-health:update")
            ->dailyAt("06:50")
            ->onOneServer();

        $schedule
            ->command("command:update-user-level")
            ->dailyAt("07:00")
            ->onOneServer();

        $schedule
            ->command("update:company-balance")
            ->dailyAt("07:50")
            ->onOneServer();

        $schedule
            ->command("email:notify-pending-document")
            ->dailyAt("08:00")
            ->onOneServer();
        $schedule
            ->command("notify:mediation")
            ->dailyAt("08:30")
            ->onOneServer();

        $schedule
            ->command("demo:create-fake-ticket")
            ->dailyAt("08:50")
            ->onOneServer();
        $schedule
            ->command("demo:create-fake-contestation")
            ->dailyAt("09:00")
            ->onOneServer();
        $schedule
            ->command("verify:boletowaitingpayment")
            ->dailyAt("09:30")
            ->onOneServer();

        $schedule
            ->command("verify:boletoexpiring")
            ->dailyAt("11:00")
            ->onOneServer();
        $schedule
            ->command("verify:boleto2")
            ->dailyAt("11:15")
            ->onOneServer();
        $schedule
            ->command("verify:abandonedcarts2")
            ->dailyAt("12:00")
            ->onOneServer();

        $schedule
            ->command("verify:coupons")
            ->dailyAt("16:30")
            ->onOneServer();

        $schedule
            ->command("demo:create-fake-ticket")
            ->dailyAt("16:50")
            ->onOneServer();
        $schedule
            ->command("demo:create-fake-contestation")
            ->dailyAt("17:00")
            ->onOneServer();

        $schedule
            ->command("verify:trackingWithoutInfo")
            ->dailyAt("18:00")
            ->onOneServer();

        $schedule
            ->command("check:menv-tracking")
            ->dailyAt("20:45")
            ->onOneServer();

        $schedule
            ->command("check:payup:chargbacks")
            ->dailyAt("23:00")
            ->onOneServer();

        $schedule
            ->command("check:company")
            ->saturdays()
            ->at("05:00")
            ->onOneServer();

        $schedule
            ->command("demo:create-fake-invite")
            ->weekly()
            ->onOneServer();
    }

    protected function commands()
    {
        $this->load(__DIR__ . "/Commands");

        require base_path("routes/console.php");
    }
}
