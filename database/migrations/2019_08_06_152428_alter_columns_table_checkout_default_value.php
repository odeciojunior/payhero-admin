<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnsTableCheckoutDefaultValue extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->integer('email_sent_amount')->default(0)->change();
            $table->integer('sms_sent_amount')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->integer('email_sent_amount')->default(null)->change();
            $table->integer('sms_sent_amount')->default(null)->change();
        });
    }
}
