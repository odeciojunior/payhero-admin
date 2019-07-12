<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsMobileTableCheckout extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->boolean('is_mobile')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->dropColumn('is_mobile');
        });
    }
}
