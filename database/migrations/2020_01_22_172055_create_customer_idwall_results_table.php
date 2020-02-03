<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientIdwallResultsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('customer_idwall_results', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index();
            $table->json('send_data');
            $table->json('received_data');
            $table->json('exception');
            $table->timestamps();
        });

        Schema::table('customer_idwall_results', function(Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('customer_idwall_results', function(Blueprint $table) {
            $table->dropForeign(["customer_id"]);
        });

        Schema::dropIfExists('customer_idwall_results');
    }
}
