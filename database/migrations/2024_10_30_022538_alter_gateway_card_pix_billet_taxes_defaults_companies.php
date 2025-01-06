<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGatewayCardPixBilletTaxesDefaultsCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            // Alterando os valores padrões das colunas
            $table->string('gateway_tax', 255)->default('6.99')->change();
            $table->string('credit_card_tax', 255)->default('6.99')->change();
            $table->string('pix_tax', 255)->default('6.99')->change();
            $table->string('boleto_tax', 255)->default('6.99')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            // Revertendo os valores padrões para 6.9
            $table->string('gateway_tax', 255)->default('6.9')->change();
            $table->string('credit_card_tax', 255)->default('6.9')->change();
            $table->string('pix_tax', 255)->default('6.9')->change();
            $table->string('boleto_tax', 255)->default('6.9')->change();
        });
    }
}
