<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNotazzIntegrationChangeTokenLogisticsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notazz_integrations', function(Blueprint $table) {
            $table->text('token_logistics')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notazz_integrations', function(Blueprint $table) {
            $table->text('token_logistics')->change();
        });
    }
}
