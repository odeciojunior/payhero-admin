<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSaleAdditionalInformationAddColumnOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE sale_additional_customer_informations ADD COLUMN `order` tinyint default 0 after label;"
        );
    }

    /**
     *
     *
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
