<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\Core\Services\FoxUtils;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gateways = [
            [
                "id" => 23,
                "name" => "voluti_production",
                "json_config" => "WwJDTElFTlR/SUQCGgIQEBAREREXEBgUERMRFBEYFBAQEBEUEQIMAkNMSUVOVH9TRUNSRVQCGgJaYkxtd3VXb2dpVHpneRR5cxAQblpxV2x3ZVh5TW1UbgJd",
                "production_flag" => 1,
            ],
            [
                "id" => 24,
                "name" => "voluti_sandbox",
                "json_config" => FoxUtils::xorEncrypt(
                    json_encode([
                        "client_id" => "",
                        "client_secret"=>""
                    ]),
                ),
                "production_flag" => 0,
            ],
        ];

        foreach ($gateways as $gateway) {
            DB::table("gateways")->insert([
                "id" => $gateway["id"],
                "gateway_enum" => 12,
                "name" => $gateway["name"],
                "json_config" => $gateway["json_config"] ?? '',
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
        ->whereIn("id", [23, 24])            
        ->delete();
    }
};
