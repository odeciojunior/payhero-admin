<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameProjectShippingsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('shippings', function(Blueprint $table) {
            $table->renameColumn('project', 'project_id');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('shippings', function(Blueprint $table) {
            $table->renameColumn('project_id', 'project');
        });
    }
}
