<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableNotazzInvocesAddCurrencyQuotationColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->unsignedBigInteger('currency_quotation_id')->after('sale_id')->nullable();
        });

        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->foreign('currency_quotation_id')->references('id')->on('currency_quotations');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->dropForeign(['currency_quotation_id']);
        });

        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->dropColumn('currency_quotation_id');
        });
    }
}
