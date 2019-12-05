<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTypeColumnMachineResultGatewayPostbacks extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('gateway_postbacks', function(Blueprint $table) {
            $table->json('machine_result2')->nullable();
        });

        \Illuminate\Support\Facades\DB::unprepared('UPDATE gateway_postbacks set machine_result2 = machine_result;');
        Schema::table('gateway_postbacks', function(Blueprint $table) {
            $table->renameColumn('machine_result', 'machine_result_old')->nullable();
        });
        Schema::table('gateway_postbacks', function(Blueprint $table) {
            $table->renameColumn('machine_result2', 'machine_result')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('gateway_postbacks', function(Blueprint $table) {
            $table->renameColumn('machine_result', 'machine_result2')->nullable();
        });
        Schema::table('gateway_postbacks', function(Blueprint $table) {
            $table->renameColumn('machine_result_old', 'machine_result')->nullable();
        });
        Schema::table('gateway_postbacks', function(Blueprint $table) {
            $table->dropColumn('machine_result2');
        });
    }
}
