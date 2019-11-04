<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAccountOwnerTableUsers extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->unsignedInteger('account_owner')->nullable();
        });

        Schema::table('users', function(Blueprint $table) {
            $table->foreign('account_owner')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropForeign(['account_owner']);
        });

        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('account_owner');
        });
    }
}
