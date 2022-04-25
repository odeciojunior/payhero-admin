<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBigidQuizIdColumnAintifraudQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('antifraud_quiz_questions', function(Blueprint $table) {
            $table->string('bigid_quiz_id')->after('sale_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('antifraud_quiz_questions', function(Blueprint $table) {
            $table->dropColumn('bigid_quiz_id');
        });
    }
}
