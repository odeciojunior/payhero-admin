<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProjectsAddOrderPriority extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_projects', function (Blueprint $table) {
            $table->integer('order_priority')->unsigned()->default(0)->after('status');
        });

        Schema::table('affiliates', function (Blueprint $table) {
            $table->integer('order_priority')->unsigned()->default(0)->after('suport_phone_verified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_projects', function (Blueprint $table) {
            $table->dropColumn('order_priority');
        });

        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropColumn('order_priority');
        });
    }
}
