<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPixelsAddAffiliateId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pixels', function(Blueprint $table) {
            $table->unsignedBigInteger('affiliate_id')->nullable()->after('purchase_card');
        });
        Schema::table('pixels', function(Blueprint $table) {
            $table->foreign('affiliate_id')->references('id')->on('affiliates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pixels', function(Blueprint $table) {
            $table->dropForeign(["affiliate_id"]);
            $table->dropColumn('affiliate_id');
        });
    }
}
