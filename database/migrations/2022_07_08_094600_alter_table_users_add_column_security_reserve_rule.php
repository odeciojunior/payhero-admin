<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;

class AlterTableUsersAddColumnSecurityReserveRule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function (Blueprint $table) {
            $table
                ->integer("security_reserve_rule")
                ->default(20)
                ->after("has_security_reserve");
        });

        $users = User::whereIn("id", [5598, 557, 3477])->get();
        foreach ($users as $user) {
            $user->update([
                "security_reserve_rule" => 10,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn(["security_reserve_rule"]);
        });
    }
}
