<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequestToSaleInformations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_informations', function (Blueprint $table) {
            $table->json('request')
                ->nullable()
                ->after('sale_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_informations', function (Blueprint $table) {
            $table->dropColumn('request');
        });
    }
}
