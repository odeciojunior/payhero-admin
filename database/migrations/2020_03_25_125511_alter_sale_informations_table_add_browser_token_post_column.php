<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSaleInformationsTableAddBrowserTokenPostColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('sale_informations', function(Blueprint $table) {
            $table->string("browser_token_post")->after("browser_token")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sale_informations', function(Blueprint $table) {
            $table->dropColumn('browser_token_post');
        });
    }
}
