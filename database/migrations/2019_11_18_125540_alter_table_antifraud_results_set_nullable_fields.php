<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAntifraudResultsSetNullableFields extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('sale_antifraud_results', function(Blueprint $table) {
            $table->json('send_data')->nullable()->change();
            $table->string('status', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sale_antifraud_results', function(Blueprint $table) {
            $table->json('send_data')->change();
            $table->string('status', 255)->change();
        });
    }
}
