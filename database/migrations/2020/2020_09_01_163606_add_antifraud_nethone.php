<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Antifraud;

class AddAntifraudNethone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Antifraud::create([
            "name" => "Nethone Production",
            "api" => "nethone",
            "antifraud_api_enum" => 3,
            "environment" => "production",
            "client_id" => "cloudfox",
            "client_secret" => "BiwtCShaAvU9LVgywyvOzgae1IKmiwrYrIeXSILFr9dxrjVcREQ8P5UVJYR2jCaSP37n2ak3ti",
            "merchant_id" => "626153",
            "available_flag" => true,
        ]);
        Antifraud::create([
            "name" => "Nethone Sandbox",
            "api" => "nethone",
            "antifraud_api_enum" => 3,
            "environment" => "sandbox",
            "client_id" => "cloudfox_intgr",
            "client_secret" => "25IryVAiChoPEmrM8i8f3zh2R3zX6mSFJULYAaGVbw1SeiaSfFytKGtHTxAqjL1x46BlEbZbzt",
            "merchant_id" => "626153",
            "available_flag" => true,
        ]);
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
