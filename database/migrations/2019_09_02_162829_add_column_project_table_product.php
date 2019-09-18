<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddColumnProjectTableProduct extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('products', function($table) {
            $table->integer('project_id')->unsigned()->index()->nullable();

            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {

        Schema::table('products', function($table) {
            $table->dropForeign(['project_id']);

            $table->dropColumn('project_id');
        });
    }
}
