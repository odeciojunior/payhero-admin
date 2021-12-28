<?php

use Modules\Core\Entities\GatewayConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertIntoGerencianetInGatewayConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        GatewayConfig::create([
            'name'=>'gerencianet',
            'gateway_id'=>19,
            'type'=>'pix',
            'type_enum'=>4
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gateway_configs', function (Blueprint $table) {
            //
        });
    }
}
