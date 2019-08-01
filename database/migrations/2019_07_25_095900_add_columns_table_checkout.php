<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsTableCheckout extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->integer('email_sent_amount')->nullable();
            $table->integer('sms_sent_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->dropColumn('email_sent_amount');
            $table->dropColumn('sms_sent_amount');
        });
    }
}
