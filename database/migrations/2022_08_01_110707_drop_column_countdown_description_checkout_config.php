<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnCountdownDescriptionCheckoutConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('checkout_configs', function (Blueprint $table) {
            $table->dropColumn(['countdown_description']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('checkout_configs', function (Blueprint $table) {
            $table->string('countdown_description')->nullable();
        });

    }
}
