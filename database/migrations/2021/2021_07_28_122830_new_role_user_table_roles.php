<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Role;

class NewRoleUserTableRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(["name" => "finantial"]);

        $userAdmin = User::create([
            "name" => "Manager finantial",
            "email" => "finantial@cloudfox.net",
            "email_verified" => "1",
            "password" => bcrypt("P0Rj7mvUaR@&F^mPLX#T"),
        ]);

        $userAdmin->assignRole("finantial");
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
}
