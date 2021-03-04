<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Modules\Core\Entities\Benefit;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserBenefit;

class PopulateUserBenefitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('command:update-user-level');

        $benefits = Benefit::all();

        User::select('id', 'level')
            ->chunk(1000, function ($users) use ($benefits) {
                foreach ($users as $user) {
                    $levelBenefits = $benefits->where('level', $user->level);
                    foreach ($levelBenefits as $benefit) {
                        UserBenefit::firstOrCreate([
                            'user_id' => $user->id,
                            'benefit_id' => $benefit->id,
                        ]);
                    }
                }
            });
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
