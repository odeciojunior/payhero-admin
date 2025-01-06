<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;

/**
 * Class CompanyGetnetHomologIdTableSeeder
 */
class CompanyGetnetHomologIdTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        if (env("APP_ENV", "local") != "production") {
            GatewaysCompaniesCredential::where("gateway_id", Gateway::GETNET_PRODUCTION_ID)->update([
                "gateway_subseller_id" => "700051332",
            ]);
            GatewaysCompaniesCredential::where("gateway_id", Gateway::GETNET_SANDBOX_ID)->update([
                "gateway_subseller_id" => "700051332",
            ]);
        }
    }
}
