<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateAnticipatedTrasactionsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('anticipated_transactions', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tax');
            $table->string('tax_value');
            $table->string('days_to_release');
            $table->bigInteger('anticipation_id')->unsigned();
            $table->bigInteger('transaction_id')->unsigned();
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions');
            $table->foreign('anticipation_id')->references('id')->on('anticipations');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('antecipated_transactions', function(Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['anticipation_id']);
        });

        Schema::dropIfExists('antecipated_transactions');
    }

}

