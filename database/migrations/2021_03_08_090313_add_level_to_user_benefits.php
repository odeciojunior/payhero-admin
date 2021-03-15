<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class AddLevelToUserBenefits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_benefits', function (Blueprint $table) {
            $table->integer('level')
                ->after('benefit_id');
        });

        Artisan::call('command:update-user-level');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_benefits', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
}
