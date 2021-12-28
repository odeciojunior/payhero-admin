<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrgazineAsaasColumnsOnCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE companies MODIFY COLUMN asaas_id varchar(255) AFTER annual_income");
        DB::statement("ALTER TABLE companies MODIFY COLUMN asaas_homolog_id varchar(255) AFTER asaas_id");
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
