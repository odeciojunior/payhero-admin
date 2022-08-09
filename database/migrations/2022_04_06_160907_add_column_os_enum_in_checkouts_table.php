<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOsEnumInCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("checkouts", function (Blueprint $table) {
            $table
                ->integer("os_enum")
                ->after("operational_system")
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
        Schema::table("checkouts", function (Blueprint $table) {
            $table->dropColumn("os_enum");
        });
    }
}
