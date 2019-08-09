<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnticipationsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('anticipations', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('value');
            $table->string('tax');
            $table->string('percentage_tax');
            $table->string('release_money_days');
            $table->integer('company_id')->unsigned();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('anticipations', function(Blueprint $table) {
            $table->dropForeign(['company_id']);
        });
        Schema::dropIfExists('anticipations');
    }
}
