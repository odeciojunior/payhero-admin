<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTransactionRateColumnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn("transaction_rate");
            $table->dropColumn("boleto_tax");
            $table->dropColumn("credit_card_tax");
            $table->dropColumn("installment_tax");
            $table->dropColumn("boleto_release_money_days");
            $table->dropColumn("credit_card_release_money_days");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("users", function (Blueprint $table) {
            $table
                ->string("transaction_rate")
                ->nullable()
                ->default("1.00");
            $table->string("boleto_tax")->nullable();
            $table->string("credit_card_tax")->nullable();
            $table
                ->string("installment_tax")
                ->nullable()
                ->default("2.99");
            $table->integer("credit_card_release_money_days")->nullable();
            $table->integer("boleto_release_money_days")->nullable();
        });
    }
}
