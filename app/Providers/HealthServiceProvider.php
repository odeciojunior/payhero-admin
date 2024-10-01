<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Services\CustomChecks\QueueSizeCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;
use Spatie\Health\Checks\Checks\DatabaseTableSizeCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class HealthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $checks = [
            DatabaseCheck::new()->name("Database check"),
            DatabaseCheck::new()
                ->connectionName("demo")
                ->name("Demo database check"),
            RedisCheck::new(),
            HorizonCheck::new(),
            DebugModeCheck::new(),
            DatabaseTableSizeCheck::new(),
            EnvironmentCheck::new(),
            UsedDiskSpaceCheck::new(),
            //OptimizedAppCheck::new(),
            //CacheCheck::new(),
        ];

        if ("Azcend-cron" === env("APP_NAME")) {
            $checks[] = ScheduleCheck::new();
            $checks[] = QueueSizeCheck::new()->maxSize(10000);
            $checks[] = DatabaseConnectionCountCheck::new()
                ->warnWhenMoreConnectionsThan(1200)
                ->failWhenMoreConnectionsThan(1600);
        }

        Health::checks($checks);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {

    }
}
