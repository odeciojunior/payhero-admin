<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateAnticipationsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('anticipations', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('value');
            $table->integer('tax');
            $table->string('percentage_tax');
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
