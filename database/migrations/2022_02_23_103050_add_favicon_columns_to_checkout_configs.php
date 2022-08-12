<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFaviconColumnsToCheckoutConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("checkout_configs", function (Blueprint $table) {
            $table
                ->boolean("checkout_favicon_enabled")
                ->after("checkout_logo")
                ->default(false);
            $table
                ->integer("checkout_favicon_type")
                ->after("checkout_favicon_enabled")
                ->default(1);
            $table
                ->string("checkout_favicon")
                ->after("checkout_favicon_type")
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("checkout_configs", function (Blueprint $table) {
            $table->dropColumn("checkout_favicon_enabled");
            $table->dropColumn("checkout_favicon_type");
            $table->dropColumn("checkout_favicon");
        });
    }
}
