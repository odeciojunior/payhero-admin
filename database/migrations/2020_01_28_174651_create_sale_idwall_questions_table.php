<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleIdwallQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {

        Schema::create('sale_idwall_questions', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id')->index();
            $table->json('question');
            $table->unsignedTinyInteger('correct_answer')->index();
            $table->unsignedTinyInteger('client_answer')->index()->nullable();
            $table->boolean('correct_flag')->index()->default(0);
            $table->timestamp('expire_at')->index();
            $table->timestamps();
        });

        Schema::table('sale_idwall_questions', function(Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sale_idwall_questions', function(Blueprint $table) {
            $table->dropForeign(["sale_id"]);
        });
        Schema::dropIfExists('sale_idwall_questions');
    }
}
