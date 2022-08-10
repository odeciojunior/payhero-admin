<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;

class UpdatedEmailWithDeletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $userModel = new User();
        $deletedUsers = $userModel->withTrashed()->get();

        foreach ($deletedUsers as $deletedUser) {
            if ($deletedUser->deleted_at !== null) {
                $deletedUser->update(["email" => uniqid() . $deletedUser->email]);
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
        //
    }
}
