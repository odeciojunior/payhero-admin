<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = DB::select("SELECT id FROM users WHERE contestation_penalties_taxes is not null");

        DB::statement(
            "UPDATE users SET contestation_penalties_taxes = null where contestation_penalties_taxes is not null"
        );

        DB::statement("ALTER TABLE `users`
        CHANGE COLUMN `contestation_penalties_taxes` `contestation_penalty_tax` INT NULL DEFAULT NULL AFTER `contestation_penalty`;");

        foreach ($users as $user) {
            DB::statement("UPDATE users SET contestation_penalty_tax = 4000 WHERE id = {$user->id}");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
