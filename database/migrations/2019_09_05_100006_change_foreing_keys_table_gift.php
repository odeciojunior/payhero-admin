<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeForeingKeysTableGift extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('gifts', function(Blueprint $table) {
            $table->renameColumn('project', 'project_id');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('gifts', function(Blueprint $table) {
            $table->renameColumn('project_id', 'project');
        });
    }
}
