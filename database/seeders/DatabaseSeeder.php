<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @return void
     */
    public function run()
    {
        $isDemo = Config::get("database.default") == "demo";
        $this->call(TasksSeeder::class);
        $this->call($isDemo ? DemoUsersSeeder::class : UsersTableSeeder::class);
        $this->call(AchievementsSeeder::class);
        $this->call($isDemo ? DemoCompaniesTableSeeder::class : CompaniesTableSeeder::class);
        $this->call($isDemo ? DemoProjectsTableSeeder::class : ProjectsTableSeeder::class);
        $this->call($isDemo ? DemoProductsSeeder::class : ProductsSeeder::class);
        $this->call($isDemo ? DemoPlansSeeder::class : PlansSeeder::class);

        if ($isDemo) {
            $this->call(DemoAppsSeeder::class);
            $this->call(DemoDiscountCouponSeeder::class);
            $this->call(DemoAchievementUserSeeder::class);
        }

        $this->call(BlockReasonsSeeder::class);
    }
}
