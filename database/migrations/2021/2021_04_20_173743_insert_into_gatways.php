<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Services\FoxUtils;

class InsertIntoGatways extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $config = [
            "client_id" => "Client_Id_a486609297d92733d9b928bb7f82ded895e232d8",
            "client_secret" => "Client_Secret_5f5dc91e2b52104c793c1e46d2e8e1b559899c0c",
        ];
        $jsonConfig = FoxUtils::xorEncrypt(json_encode($config));

        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (18,16,'gerencianet_production','{$jsonConfig}',1,1,null,NOW(),NOW());");

        $config = [
            "client_id" => "Client_Id_1acaf26d20a4b278480aaf21cb03140727189d13",
            "client_secret" => "Client_Secret_a3468dbecbe187c6e70cadbd6b15b0f66a5ba09a",
        ];
        $jsonConfig = FoxUtils::xorEncrypt(json_encode($config));

        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (19,16,'gerencianet_sandbox','{$jsonConfig}',0,1,null,NOW(),NOW());");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DELETE gateways WHERE id IN (18,19)");
    }
}
