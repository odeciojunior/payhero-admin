<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeProjectsTableRemoveColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function(Blueprint $table) {
            $table->dropColumn('shipment');
            $table->dropColumn('shipment_fixed');
            $table->dropColumn('shipment_value');
            $table->dropColumn('shipment_responsible');
            $table->dropColumn('sms_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function(Blueprint $table) {
            $table->string('shipment')->nullable();
            $table->string('shipment_fixed')->nullable();
            $table->string('shipment_value')->nullable();
            $table->string('shipment_responsible')->nullable();
            $table->string('sms_status')->nullable();
        });
    }
}
