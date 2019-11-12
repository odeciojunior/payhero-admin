<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableNotazzIntegrationAddPendingDaysColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notazz_integrations', function(Blueprint $table) {
            $table->unsignedInteger('pending_days')->default(1); //1, 7, 15, 30, 60
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
            $table->dropColumn('pending_days');
        });
    }
}
