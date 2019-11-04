<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\User;

class UpdateAccountOwnerTableUsers extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $userModel = new User();
        $users     = $userModel->all();
        foreach ($users as $user) {
            $user->update(['account_owner' => $user->id]);
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        $userModel = new User();
        $users     = $userModel->all();
        foreach ($users as $user) {
            $user->update(['account_owner' => null]);
        }
    }
}
