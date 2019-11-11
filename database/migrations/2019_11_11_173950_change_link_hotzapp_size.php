<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLinkHotzappSize extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('hotzapp_integrations', function(Blueprint $table) {
            $table->text('link')->change();
        });

        Schema::table('convertax_integrations', function(Blueprint $table) {
            $table->text('link')->change();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('hotzapp_integrations', function(Blueprint $table) {
            $table->string('link', 255)->change();
        });
        Schema::table('convertax_integrations', function(Blueprint $table) {
            $table->string('link', 255)->change();
        });
    }
}
