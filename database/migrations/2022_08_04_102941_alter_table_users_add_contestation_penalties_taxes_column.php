<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;

class AlterTableUsersAddContestationPenaltiesTaxesColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('contestation_penalties_taxes')->after('contestation_penalty')->nullable();
        });

        // Default penalty values
        $data = [
            'contestation_penalty_level_1' => 2000,
            'contestation_penalty_level_2' => 3000,
            'contestation_penalty_level_3' => 5000,
        ];

        // Update records with default values
        User::query()->update(['contestation_penalties_taxes' => json_encode($data)]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['contestation_penalties_taxes']);
        });
    }
}
