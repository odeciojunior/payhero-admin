<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Benefit;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserBenefit;

class AlterUserBenefits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_benefits', function (Blueprint $table) {
            $table->dropColumn(['level', 'disabled']);
            $table->boolean('enabled')
                ->default(0)
                ->after('benefit_id');
        });

        $users = User::select('id')->get();
        $benefits = Benefit::select('id')->get();
        foreach ($users as $user){
            foreach ($benefits as $benefit){
                UserBenefit::create([
                    'user_id' => $user->id,
                    'benefit_id' => $benefit->id
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_benefits', function (Blueprint $table) {
            $table->dropColumn('enabled');
            $table->integer('level')
                ->after('benefit_id');
            $table->boolean('disabled')
                ->default(0)
                ->after('level');
        });
    }
}
