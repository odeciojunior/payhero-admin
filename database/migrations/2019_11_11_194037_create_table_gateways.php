<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Services\FoxUtils;

class CreateTableGateways extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('gateways', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('gateway_enum')->index();
            $table->string('name');
            $table->text('json_config');
            $table->tinyInteger('production_flag')->default(0);
            $table->tinyInteger('enabled_flag')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });

        $config = [
            'public_key'     => '',
            'encryption_key' => '',
        ];

        $jsonConfig = FoxUtils::xorEncrypt(json_encode($config));
        $sql        = "INSERT INTO gateways (gateway_enum, name, json_config, production_flag, enabled_flag) ";
        $sql        .= "VALUES(1, 'pagarme_production','" . $jsonConfig . "', 1, 0)";
        DB::select($sql);

        $config     = [
            'public_key'     => 'ak_test_O22Fy0HHJt1dh5Mg0vZFtqbJPApQZf',
            'encryption_key' => 'ek_test_4xkCDbiE5MDMpZmMEnb1ZH9Sm7Ifux',
        ];
        $jsonConfig = FoxUtils::xorEncrypt(json_encode($config));
        $sql        = "INSERT INTO gateways (gateway_enum, name, json_config, production_flag, enabled_flag) ";
        $sql        .= "VALUES(2, 'pagarme_sandbox','" . $jsonConfig . "', 0, 0)";
        DB::select($sql);

        $config     = [
            'marketplace_id'  => 'ce02cc0783794e7cb1fc8fdc46ef953b',
            'publishable_key' => 'zpk_prod_RZx0pdCvK3LKQ55Nj2QGKani',
            'seller_id'       => 'a1f907cd215040729f9f19b7f2df4ec3',
        ];
        $jsonConfig = FoxUtils::xorEncrypt(json_encode($config));
        $sql        = "INSERT INTO gateways (gateway_enum, name, json_config, production_flag, enabled_flag) ";
        $sql        .= "VALUES(3, 'zoop_production','" . $jsonConfig . "', 1, 0)";
        DB::select($sql);

        $config     = [
            'marketplace_id'  => '72664906b4444f0fa900098844baf84b',
            'publishable_key' => 'zpk_test_4UfSOSzIS895VJOt19zDAI1U',
            'seller_id'       => '43e2543afd5c45e7b219a26c36551bdf',
        ];
        $jsonConfig = FoxUtils::xorEncrypt(json_encode($config));
        $sql        = "INSERT INTO gateways (gateway_enum, name, json_config, production_flag, enabled_flag) ";
        $sql        .= "VALUES(4, 'zoop_sandbox','" . $jsonConfig . "', 0, 0)";
        DB::select($sql);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gateways');
    }
}
