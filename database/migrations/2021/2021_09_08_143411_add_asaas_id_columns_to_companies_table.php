<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAsaasIdColumnsToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table->string("asaas_id");
            $table->string("asaas_homolog_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table->dropColumn("asaas_id");
            $table->dropColumn("asaas_homolog_id");
        });
    }
}
