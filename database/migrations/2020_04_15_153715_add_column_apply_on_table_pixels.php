<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnApplyOnTablePixels extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('pixels', function(Blueprint $table) {
            $table->json('apply_on_plans')->nullable()->nullable()
                  ->after('campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('pixels', function(Blueprint $table) {
            $table->dropColumn('apply_on_plans');
        });
    }
}
