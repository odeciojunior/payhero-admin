<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTrackings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trackings', function ($table){
           $table->dropColumn('tracking_date');
           $table->dropColumn('description');
           $table->string('tracking_code')->after('delivery_id');
           $table->integer('tracking_status_enum')->after('tracking_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trackings', function ($table){
            $table->dropColumn('tracking_code');
            $table->dropColumn('tracking_status_enum');
            $table->timestamp('tracking_date')->after('delivery_id');
            $table->string('description')->after('tracking_date');
        });
    }
}
