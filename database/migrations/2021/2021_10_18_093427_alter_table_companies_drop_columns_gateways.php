<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCompaniesDropColumnsGateways extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `companies`
        DROP COLUMN get_net_status, 
        DROP COLUMN subseller_getnet_id,
        DROP COLUMN subseller_getnet_homolog_id,
        DROP COLUMN asaas_id,
        DROP COLUMN asaas_homolog_id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
