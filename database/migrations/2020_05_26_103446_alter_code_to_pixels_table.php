<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AlterCodeToPixelsTable
 */
class AlterCodeToPixelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'pixels',
            function (Blueprint $table) {
                $table->string('code', 100)->change();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'pixels',
            function (Blueprint $table) {
                $table->string('code', 30)->change();
            }
        );
    }
}
