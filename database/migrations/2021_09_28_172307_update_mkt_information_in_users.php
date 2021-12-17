<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMktInformationInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = \Modules\Core\Entities\User::whereNotNull('mkt_information')->get();

        foreach ($users as $user) {
            $mktInformation = json_decode($user->mkt_information, true);

            $user->mkt_information = [
                'monthly_income' => $mktInformation['monthly_income'] == -1 ? 0 : $mktInformation['monthly_income'],
                'website_url' => $mktInformation['website_url'],
                'gateway' => $mktInformation['gateway'],
                'niche' => is_array($mktInformation['niche']) ? $mktInformation['niche'] : json_decode($mktInformation['niche'], true),
                'store' => is_array($mktInformation['store']) ? $mktInformation['store'] : json_decode($mktInformation['store'], true),
                'cloudfox_referer' => is_array($mktInformation['cloudfox_referer']) ? $mktInformation['cloudfox_referer'] : json_decode($mktInformation['cloudfox_referer'], true),
            ];

            $user->save();
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
