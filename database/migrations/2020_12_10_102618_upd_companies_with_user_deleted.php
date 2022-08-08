<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdCompaniesWithUserDeleted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = \Modules\Core\Entities\User::onlyTrashed()
            ->whereRaw("id", "account_owner_id")
            ->get();

        foreach ($users as $user) {
            foreach ($user->companies as $company) {
                $company->delete();
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
