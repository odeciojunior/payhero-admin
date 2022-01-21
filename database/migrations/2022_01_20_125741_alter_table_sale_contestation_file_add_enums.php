<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSaleContestationFileAddEnums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `sale_contestation_files`
        CHANGE COLUMN `type` `type` ENUM('NOTA_FISCAL','POLITICA_VENDA','ENTREGA','INFO_ACORDO','TERMOS_USO','POLITICA_CANCEL','COMPROVANTE_CANCEL','OUTROS') 
        NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `user_id`;");
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
