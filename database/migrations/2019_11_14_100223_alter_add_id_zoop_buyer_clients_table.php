<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddIdZoopBuyerClientsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function(Blueprint $table) {
            $table->string('id_zoop_buyer', 255)->nullable()->after('id_kapsula_client');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function(Blueprint $table) {
            $table->dropColumn('id_zoop_buyer');
        });
    }
}
