<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\GatewayFlag;
use Modules\Core\Entities\GatewayFlagTax;
use Modules\Core\Services\FoxUtils;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $jsonConfig = 'WwJDTElFTlR/SUQCGgITFBgSRBMXFUFEERhFFUUSQRkYGEIWRUZFEBgTFkUYGRQYFhBCERARQkMYRBUTRkNDGUQVGUMYF0FDRhRCFhUYRRYXQREUGEMWFkNFE0FFRBBBGUQQAgwCQ0xJRU5Uf1NFQ1JFVAIaAhYWQ0UTQUVEEEEZRBETFBgSRBMXFUFEERhFFUUSQRkYGEIWRUZFEBgTFkUYGRFBRBFCQUYWFUITFEEZFhgVGUZFFxgRRRlCEERFFBFFQkQSFUEVQ0QSREYXEUZBE0QWFUMZFBlFEkFBQRkTEUUSRhgTFRcVExEQGBBDRhdFAgwCQVVUSH9CQVNJQwIaAkISckx5EkxWcWdmFnkSdlV6YxVKQhIQVXlOaRZxeFBiQmhiT3l0aVoCXQ==';
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (21,10,'armpay_production','{$jsonConfig}',1,1,null,NOW(),NOW());");

        $jsonConfig2 = $jsonConfig;
        DB::statement("INSERT INTO `gateways`
        (`id`,`gateway_enum`,`name`,`json_config`,`production_flag`,`enabled_flag`,`deleted_at`,`created_at`,`updated_at`)
        VALUES (22,10,'armpay_sandbox','{$jsonConfig2}',0,1,null,NOW(),NOW());");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DELETE FROM `gateway_flag_taxes` WHERE gateway_flag_id in (SELECT id FROM gateway_flags WHERE gateway_id in (13, 14));");

        DB::statement("DELETE FROM `gateway_flags` WHERE gateway_id in (13, 14);");

        DB::statement("DELETE FROM `gateways` WHERE id in (13, 14);");
    }
};
