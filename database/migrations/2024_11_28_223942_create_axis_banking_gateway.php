<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Services\FoxUtils;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gateways = [
            [
                "id" => 25,
                "name" => "axisbanking_production",
                "json_config" => "WwJTRUNSRVR/S0VZAhoCExRBRhYSGEINRhIVQw0UGENGDRkZEUENEkJCFBVEExdFEUUWAl0=",
                "production_flag" => 1,
            ],
            [
                "id" => 26,
                "name" => "axisbanking_sandbox",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode([
                        "secret_key" => "",
                    ])
                ),
                "production_flag" => 0,
            ],
        ];

        foreach ($gateways as $gateway) {
            DB::table("gateways")->insert([
                "id" => $gateway["id"],
                "gateway_enum" => 12,
                "name" => $gateway["name"],
                "json_config" => $gateway["json_config"] ?? "",
                "production_flag" => $gateway["production_flag"],
                "enabled_flag" => 1,
                "deleted_at" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table("gateways")
            ->whereIn("id", [25, 26])
            ->delete();
    }
};
