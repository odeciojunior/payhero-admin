<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAnticipationsTableAddColumnPercentageAnticipable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('anticipations', function(Blueprint $table) {
            $table->string('percentage_anticipable')->after('percentage_tax')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('anticipations', function(Blueprint $table) {
            $table->dropColumn('percentage_anticipable');
        });
    }

}
