<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;

class AddObservationInUsersAndDropUsersNeverLogged extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function (Blueprint $table) {
            $table->text("observation")->after("onboarding");
        });

        $users = User::whereNull("last_login")
            ->where("created_at", "<=", "2020-10-31")
            ->whereRaw("id = account_owner_id")
            ->get();
        foreach ($users as $user) {
            $user->update([
                "email" => uniqid() . $user->email,
            ]);

            $user->delete();
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
            $table->dropColumn("observation");
        });
    }
}
