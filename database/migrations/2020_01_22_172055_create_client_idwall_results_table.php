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
        Schema::create('client_idwall_results', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->index();
            $table->json('send_data');
            $table->json('received_data');
            $table->json('exception');
            $table->timestamps();
        });

        Schema::table('client_idwall_results', function(Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('client_idwall_results', function(Blueprint $table) {
            $table->dropForeign(["client_id"]);
        });

        Schema::dropIfExists('client_idwall_results');
    }
}
