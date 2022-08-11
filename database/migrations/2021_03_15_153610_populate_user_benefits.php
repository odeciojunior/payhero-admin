<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\UserBenefit;

class PopulateUserBenefits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('INSERT INTO user_benefits (user_id, benefit_id, created_at)
                       SELECT DISTINCT users.id, benefits.id, now() FROM users, benefits
                       WHERE NOT EXISTS(SELECT * FROM user_benefits WHERE user_id = users.id AND benefits.id = benefit_id)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        UserBenefit::query()->delete();
    }
}
