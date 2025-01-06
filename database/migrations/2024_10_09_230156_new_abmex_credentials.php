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
        DB::statement("UPDATE `gateways` SET json_config ='WwJBUEl/S0VZAhoCERcTFlxFeGFFbXNZTG5mF3FTQkRuURdXeW1UV3oXa21NeBRQbWt2dxdaUVZ3FxlDFxkWRRkCXQ==',
        updated_at =NOW() WHERE id=11;");

        DB::statement("UPDATE `gateways` SET json_config ='WwJBUEl/S0VZAhoCERcTFlxFeGFFbXNZTG5mF3FTQkRuURdXeW1UV3oXa21NeBRQbWt2dxdaUVZ3FxlDFxkWRRkCXQ==',
        updated_at =NOW() WHERE id=12;");
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
