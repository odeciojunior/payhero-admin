<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleWhiteBlackListResultsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('sale_white_black_list_results', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("sale_id");
            $table->boolean('whitelist')->comment('True (Está na whitelist) - False (Não está na whitelist)');
            $table->boolean('blacklist')->comment('True (Está na blacklist) - False (Não está na blacklist)');
            $table->json('whiteblacklist_json')->comment('Regras que caíram no black/white list');
            $table->timestamps();
        });

        Schema::table('sale_white_black_list_results', function(Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sale_white_black_list_results', function(Blueprint $table) {
            $table->dropForeign(["sale_id"]);
        });

        Schema::dropIfExists('sale_white_black_list_results');
    }
}
