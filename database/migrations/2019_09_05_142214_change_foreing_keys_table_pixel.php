<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForeingKeysTablePixel extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('pixels', function(Blueprint $table) {
            $table->renameColumn('campaign', 'campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('pixels', function(Blueprint $table) {
            $table->renameColumn('campaign_id', 'campaign');
        });
    }
}
