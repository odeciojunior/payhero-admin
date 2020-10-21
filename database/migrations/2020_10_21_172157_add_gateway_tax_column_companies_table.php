<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGatewayTaxColumnCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('gateway_tax')->nullable()->default('6.9')->after('active_flag');
            $table->string('boleto_tax')->nullable()->after('gateway_tax');
            $table->string('credit_card_tax')->nullable()->after('boleto_tax');
            $table->string('installment_tax')->nullable()->default('2.99')->after('credit_card_tax');
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
            $table->dropColumn(['gateway_tax', 'boleto_tax', 'credit_card_tax', 'installment_tax']);
        });
    }
}
