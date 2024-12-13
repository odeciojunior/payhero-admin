<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Achievement;

class DemoAchievementUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $achievements = Achievement::get();

        foreach ($achievements as $item) {
            DB::statement(
                "INSERT INTO `achievement_user` (`achievement_id`, `user_id`, `created_at`, `updated_at`) VALUES ({$item->id}, 1, now(), now());"
            );
        }
    }
}
