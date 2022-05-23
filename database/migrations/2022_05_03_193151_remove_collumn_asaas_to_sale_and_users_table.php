<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCollumnAsaasToSaleAndUsersTable extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //asaas_alert e anticipation_id 'anticipation_status', 'anticipation_id' antifraud_observation
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['anticipation_status', 'anticipation_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('asaas_alert');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('anticipation_status')->nullable()->after('antifraud_observation');
            $table->string('anticipation_id')->nullable()->after('anticipation_status');;
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('asaas_alert')->default(false)->after('mkt_information');;
        });

    }
}
