<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("UPDATE `gateways` SET json_config ='WwJUT0tFTgIaAmRmYhkYYRQXZhJhGGUQFRgQFxUZZWYTEBVlZRIVFBgQYhYWYxcRZhYSEBdlEGQRZRdkEWQVYxUTFRgTEGRiE2UCDAJBQ0NPVU5Uf0lEAhoCZmRkZBISYWQZGBUVFGVhFBhiZhYWYhUTGRMUFGYXFRYCDAJUT0tFTn9SU0ECGgIUEBkRFBUWERcUExIWYRgTFRJhFhdkEhQUERgYEGFjZRZkZhgQERAWY2EYExRkEmESExUZZRhlZBVlFhcRZRRjAl0=',
        updated_at =NOW() WHERE id=7;");
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
};
