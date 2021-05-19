,<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsTablePlans extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function($table) {
            $table->dropForeign('planos_transportadora_foreign');
            $table->dropForeign('planos_empresa_foreign');

            $table->dropColumn('company');
            $table->dropColumn('photo');
            $table->dropColumn('amount');
            $table->dropColumn('id_plan_carrier');
            $table->dropColumn('carrier');
            $table->dropColumn('hotzapp_integration');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function($table) {

            $table->integer('company')->unsigned();
            $table->string('photo');
            $table->integer('amount');
            $table->string('id_plan_carrier');
            $table->integer('carrier');
            $table->integer('hotzapp_integration');

            $table->integer('company')->references('id')->on('companies');
            $table->integer('carrier')->references('id')->on('carriers');
        });
    }
}
