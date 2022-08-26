<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;
use Spatie\Health\Checks\Checks\DatabaseTableSizeCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;

class HealthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $checks = [
            DatabaseCheck::new()->name('Database check'),
            DatabaseCheck::new()->connectionName('demo')->name('Demo database check'),
            RedisCheck::new(),
            PingCheck::new()->url('https://sirius.cloudfox.net')->timeout(5)->name('Sirius check'),
            PingCheck::new()->url('https://sac.cloudfox.net')->timeout(5)->name('Sac check'),
            PingCheck::new()->url('https://manager.cloudfox.net')->timeout(5)->name('Manager check'),
            PingCheck::new()->url('https://accounts.cloudfox.net')->timeout(5)->name('Accounts check'),
            PingCheck::new()->url('https://checkout.cloudfox.net')->timeout(5)->name('Checkout check'),
            //OptimizedAppCheck::new(),
            HorizonCheck::new(),
            DebugModeCheck::new(),
            DatabaseTableSizeCheck::new(),
            DatabaseConnectionCountCheck::new()->warnWhenMoreConnectionsThan(400)->failWhenMoreConnectionsThan(600),
            CacheCheck::new(),
            EnvironmentCheck::new(),
            UsedDiskSpaceCheck::new(),
        ];

        if(env('APP_NAME') == 'Cloudfox-cron') {
            $checks[] = ScheduleCheck::new();
        }

        Health::checks($checks);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
