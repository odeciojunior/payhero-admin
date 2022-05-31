<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFksToCheckoutConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkout_configs', function (Blueprint $table) {
            $table->unsignedInteger('project_id')->change();
            $table->unsignedInteger('company_id')->change();
        });

        Schema::table('checkout_configs', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkout_configs', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->change();
            $table->unsignedBigInteger('company_id')->change();
        });

        Schema::table('checkout_configs', function (Blueprint $table) {
            $table->dropForeign(['project_id', 'company_id']);
        });
    }
}
