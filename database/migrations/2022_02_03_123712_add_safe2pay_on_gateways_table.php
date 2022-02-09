<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\FoxUtils;

class AddSafe2payOnGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Gateway::updateOrCreate(
            [
                "name" => "safe2pay_sandbox",
            ],
            [
                "gateway_enum"    => 17,
                "name"            => "safe2pay_sandbox",
                "json_config"     => FoxUtils::xorEncrypt(
                    json_encode(
                        [
                            "token"  => "902F8C28AF904CA2BFAF20C9EA403CFF",
                            "secret_key"  => "1F991670D3434E3EB45721C255B4A01545A1B2FBA39549AB9D8828C8853E8E02"
                        ]
                    )),
                "production_flag" => 0,
                "enabled_flag"    => 1,
            ]
        );
    
        Gateway::updateOrCreate(
            [
                "name" => "safe2pay_production",
            ],
            [
                "gateway_enum"    => 17,
                "name"            => "safe2pay_production",
                "json_config"     => FoxUtils::xorEncrypt(
                    json_encode(
                        [
                            "token"  => "902F8C28AF904CA2BFAF20C9EA403CFF",
                            "secret_key"  => "1F991670D3434E3EB45721C255B4A01545A1B2FBA39549AB9D8828C8853E8E02"
                        ]
                    )),
                "production_flag" => 1,
                "enabled_flag"    => 1,
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
        $safe2paySandbox = Gateway::where('name', 'safe2pay_sandbox')->first();
        $safe2paySandbox->forceDelete();

        $safe2payProduction = Gateway::where('name', 'safe2pay_production')->first();
        $safe2payProduction->forceDelete();
    }
}
