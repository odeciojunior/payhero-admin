<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOsEnumInAntifraudDeviceDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("antifraud_device_data", function (Blueprint $table) {
            $table
                ->integer("os_enum")
                ->index()
                ->after("os")
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("antifraud_device_data", function (Blueprint $table) {
            $table->dropColumn("os_enum");
        });
    }
}
