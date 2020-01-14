<?php

use Modules\Core\Entities\User;
use Modules\Core\Entities\Client;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePhoneNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // User::whereNotNull('cellphone')->chunk(100, function($users){
        //     foreach($users as $user){
        //         $user->update([
        //             'cellphone' => '+55' . preg_replace("/[^0-9]/", "", $user->cellphone)
        //         ]);
        //     }
        // });

        // Company::whereNotNull('support_telephone')->chunk(100, function($companies){
        //     foreach($companies as $company){
        //         $company->update([
        //             'support_telephone' => '+55' . preg_replace("/[^0-9]/", "", $company->support_telephone)
        //         ]);
        //     }
        // });

        // Client::whereNotNull('telephone')->chunk(100, function($clients){
        //     foreach($clients as $client){
        //         $client->update([
        //             'telephone' => '+55' . preg_replace("/[^0-9]/", "", $client->telephone)
        //         ]);
        //     }
        // });
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
