<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductsPlansAddColumnCustomConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `products_plans` ADD COLUMN `custom_config` JSON NULL DEFAULT NULL AFTER `amount`;");
        DB::statement("ALTER TABLE `plans` DROP COLUMN `config_personalization_product`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `products_plans` DROP COLUMN `custom_config`;");
        DB::statement(
            "ALTER TABLE `plans` ADD COLUMN `config_personalization_product` JSON NULL DEFAULT NULL AFTER `shopify_variant_id`;"
        );
    }
}
