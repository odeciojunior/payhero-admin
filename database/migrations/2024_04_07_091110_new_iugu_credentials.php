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
        DB::statement("UPDATE `gateways` SET json_config ='WwJUT0tFTgIaAmRmYhkYYRQXZhJhGGUQFRgQFxUZZWYTEBVlZRIVFBgQYhYWYxcRZhYSEBdlEGQRZRdkEWQVYxUTFRgTEGRiE2UCDAJBQ0NPVU5Uf0lEAhoCZmRkZBISYWQZGBUVFGVhFBhiZhYWYhUTGRMUFGYXFRYCXQ==',
        updated_at =NOW() WHERE id=7;");

        DB::statement("UPDATE `gateways` SET json_config ='WwJUT0tFTgIaAmEZZhUYEBMUFhkWFBMUY2ZjYxMVFmUWYWERF2FiFmJiEhYVERkYERQUERgUFmEQYmZhE2QTFmUXZRMWEGERYhICDAJBQ0NPVU5Uf0lEAhoCZmRkZBISYWQZGBUVFGVhFBhiZhYWYhUTGRMUFGYXFRYCXQ==',
        updated_at =NOW() WHERE id=8;");
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
