<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Antifraud;

class InsertSeonAntifraudRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Antifraud::create(
            [
                "id"                 => 6,
                "name"               => "Seon Sandbox",
                "api"                => "seon",
                "antifraud_api_enum" => 4,
                "environment"        => "sandbox",
                "client_id"          => env('SEON_SANDBOX_PUBLIC_KEY'),
                "client_secret"      => env('SEON_SANDBOX_LICENSE_KEY'),
                "merchant_id"        => 'Cloudfox',
                "available_flag"     => 1
            ]
        );

        Antifraud::create(
            [
                "id"                 => 7,
                "name"               => "Seon Production",
                "api"                => "seon",
                "antifraud_api_enum" => 4,
                "environment"        => "production",
                "client_id"          => env('SEON_PRODUCTION_PUBLIC_KEY'),
                "client_secret"      => env('SEON_PRODUCTION_LICENSE_KEY'),
                "merchant_id"        => 'Cloudfox',
                "available_flag"     => 1
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DELETE FROM antifrauds WHERE api = 'seon'");
    }
}
