<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnsTrackingTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('trackings', function($table) {

            $table->dropColumn('tracking_code');
            $table->dropColumn('tracking_type_enum');
            $table->dropColumn('tracking_status_enum');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('trackings', function($table) {

            $table->string('tracking_code');
            $table->tinyInteger('tracking_type_enum');
            $table->tinyInteger('tracking_status_enum');
        });
    }
}
