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
        /*
            Syncing the schedule
            php artisan schedule-monitor:sync

            List the schedule
            php artisan schedule-monitor:list

            Naming tasks
            ->monitorName('a-custom-name');

            Setting a grace time
            ->graceTimeInMinutes(10);

            Ignoring scheduled tasks
            ->doNotMonitor();

            Storing output in the database
            ->storeOutputInDb();
        */

        setlocale(LC_ALL, "pt_BR");

        $schedule->command(ScheduleCheckHeartbeatCommand::class)->everyMinute();
        $schedule
            ->command("change:pix-to-canceled")
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command("horizon:snapshot")->everyFiveMinutes();
        $schedule
            ->command("gatewaypostbacks:process")
            ->withoutOverlapping()
            ->everyFiveMinutes();

        $schedule->command("ethoca:proccess-postback")->everyTenMinutes();
        $schedule
            ->command("demo:create-fake-checkout")
            ->everyTenMinutes()
            ->withoutOverlapping();

        $schedule->command("check:underattack")->everyThirtyMinutes();
        $schedule
            ->command("withdrawals:release-get-faster")
            ->withoutOverlapping()
            ->everyThirtyMinutes();
        $schedule->command("generate:notazzinvoicessalesapproved")->everyThirtyMinutes();
        $schedule
            ->command("verify:pendingnotazzinvoices")
            ->everyThirtyMinutes()
            ->withoutOverlapping();
        $schedule
            ->command("verify:abandonedcarts")
            ->everyFifteenMinutes()
            ->withoutOverlapping();
        $schedule
            ->command("demo:create-fake-sale")
            ->withoutOverlapping()
            ->everyThirtyMinutes();

        $schedule->command("verify:pendingdomains")->hourly();
        $schedule
            ->command("verify:tickets")
            ->withoutOverlapping()
            ->hourly();
        $schedule
            ->command("verify:tickets-refunded")
            ->withoutOverlapping()
            ->hourly();

        $schedule
            ->command("command:checkUpdateCompanyGetnet")
            ->sundays()
            ->at("05:00");
        $schedule
            ->command("demo:create-fake-withdrawal")
            ->days([Schedule::MONDAY, Schedule::WEDNESDAY, Schedule::FRIDAY])
            ->at("05:20");
        $schedule
            ->command("woocommerce:check-tracking-codes")
            ->sundays()
            ->at("06:30");
        $schedule
            ->command("asaas:transfers-surplus-balance")
            ->mondays()
            ->at("06:25");
        $schedule
            ->command("getnet:check-withdrawals-released-cloudfox")
            ->mondays()
            ->at("22:00");
        $schedule
            ->command("getnet:check-withdrawals-liquidated-cloudfox")
            ->mondays()
            ->at("22:30");

        $schedule->command("whiteblacklist:verifyexpires")->dailyAt("00:00");
        $schedule->command("asaas:transfers-chargebacks")->dailyAt("00:20");
        $schedule->command("check:has-valid-tracking")->dailyAt("00:30");
        $schedule->command("verify:inviteexpired")->dailyAt("00:40");
        $schedule->command("account-health:user:update-average-response-time")->dailyAt("00:50");
        $schedule->command("getnet:release-unblocked-balance")->dailyAt("01:10");
        $schedule->command("command:ShopifyReorderSales")->dailyAt("01:30");
        $schedule->command("getnet:check-refunded")->dailyAt("02:00");
        $schedule->command("getnet:block-sale-for-contestation")->dailyAt("02:20");
        $schedule->command("command:WoocommerceReorderSales")->dailyAt("02:45");
        $schedule->command("check:automatic-withdrawals")->dailyAt("03:10");
        $schedule->command("command:deleteTemporaryFiles")->dailyAt("03:20");
        $schedule->command("getnet:update-confirm-date-debt-pending")->dailyAt("03:30");
        $schedule->command("safe2pay:resend-bankslip-webhook")->dailyAt("03:40");
        $schedule->command("command:validateLastDomains")->dailyAt("04:00");
        $schedule->command("command:WoocommerceRetryFailedRequests")->dailyAt("04:15");
        $schedule->command("under-attack:update-card-declined")->dailyAt("05:00");
        $schedule->command("command:UpdateListsFoxActiveCampaign")->dailyAt("05:10");
        $schedule->command("change:boletopendingtocanceled")->dailyAt("05:20");
        $schedule->command("tasks:check-completed-sales-tasks")->dailyAt("05:30");
        $schedule->command("demo:abandoned-cart-checkout")->dailyAt("05:35");
        $schedule->command("check:gateway-tax-company-after-month")->dailyAt("05:40");
        $schedule->command("getnet:get-all-statement-chargebacks")->dailyAt("05:45");

        $schedule->command("available-balance:update")->dailyAt("06:15");
        $schedule->command("demo:update-financial-fake-data")->dailyAt("06:20");
        $schedule->command("user:benefits:update")->dailyAt("06:30");
        $schedule->command("achievements:update")->dailyAt("06:40");
        $schedule->command("account-health:update")->dailyAt("06:50");

        $schedule->command("command:update-user-level")->dailyAt("07:00");
        $schedule->command("updateTransactionsReleaseDate")->dailyAt("07:15");
        $schedule->command("update:currencyquotation")->dailyAt("07:20");
        $schedule->command("balance:unlock")->dailyAt("07:45");

        // $schedule->command("pipefy:first-sale")->dailyAt("07:35");
        // $schedule->command("pipefy:top-sale")->dailyAt("07:45");
        $schedule->command("update:company-balance")->dailyAt("07:50");

        $schedule->command("email:notify-pending-document")->dailyAt("08:00");
        $schedule->command("notify:mediation")->dailyAt("08:30");

        $schedule->command("demo:create-fake-ticket")->dailyAt("08:50");
        $schedule->command("demo:create-fake-contestation")->dailyAt("09:00");
        $schedule->command("verify:boletowaitingpayment")->dailyAt("09:30");

        $schedule->command("verify:boletoexpiring")->dailyAt("11:00");
        $schedule->command("verify:boleto2")->dailyAt("11:15");
        $schedule->command("verify:abandonedcarts2")->dailyAt("12:00");

        $schedule->command("verify:coupons")->dailyAt("16:30");

        $schedule->command("demo:create-fake-ticket")->dailyAt("16:50");
        $schedule->command("demo:create-fake-contestation")->dailyAt("17:00");

        $schedule->command("verify:trackingWithoutInfo")->dailyAt("18:00");

        $schedule->command("safe2pay:update-reason-sale-contestations")->dailyAt("19:30");

        $schedule->command("antifraud:backfill-asaas-chargebacks")->dailyAt("20:00");
        //        $schedule->command("antifraud:check-document-on-bureau")->everyTenMinutes();
        $schedule->command("getnet:check-withdrawals-released")->dailyAt("20:30");
        $schedule->command("check:menv-tracking")->dailyAt("20:45");
        $schedule->command("getnet:import-sale-contestations-txt-format")->dailyAt("21:00");
        $schedule->command("getnet:import-sale-contestations")->dailyAt("21:30");
        $schedule->command("getnet:check-withdrawals-released")->dailyAt("22:30");
        $schedule->command("getnet:check-withdrawals-liquidated")->dailyAt("23:30");
        $schedule->command("verify:promotional-tax")->dailyAt("23:45");

        $schedule
            ->command("check:company")
            ->saturdays()
            ->at("05:00");

        $schedule->command("demo:create-fake-invite")->weekly();
        // $schedule->command('verify:boletopaid')->dailyAt('10:30');  remover dependencias
    }

    protected function commands()
    {
        $this->load(__DIR__ . "/Commands");

        require base_path("routes/console.php");
    }
}
