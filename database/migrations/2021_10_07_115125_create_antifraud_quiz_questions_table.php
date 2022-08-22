<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAntifraudQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("antifraud_quiz_questions", function (Blueprint $table) {
            $table->id();
            $table->foreignId("sale_id")->constrained();
            $table->string("question", 255);
            $table->string("correct_answer", 255);
            $table->string("answer", 255)->nullable();
            $table->boolean("open_answer_flag")->nullable();
            $table->dateTime("started_at")->nullable();
            $table->dateTime("finished_at")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("antifraud_quiz_questions");
    }
}
