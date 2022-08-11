<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSaleAdditionalCustomerInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `sale_additional_customer_informations` 
        CHANGE COLUMN `text` `type_enum` ENUM('File', 'Image', 'Text') NULL DEFAULT 'Text' AFTER `product_id`,
        CHANGE COLUMN `file` `value` VARCHAR(250) NULL DEFAULT NULL ;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `sale_additional_customer_informations` 
        CHANGE COLUMN `type_enum` `text` varchar(250) NULL DEFAULT NULL AFTER `product_id`,
        CHANGE COLUMN `value` `file` VARCHAR(1000) NULL DEFAULT NULL ;");
    }
}
