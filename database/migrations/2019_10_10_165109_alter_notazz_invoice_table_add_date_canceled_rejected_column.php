<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNotazzInvoiceTableAddDateCanceledRejectedColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->string('date_canceled')->after('return_message')->nullable();
            $table->string('date_rejected')->after('return_message')->nullable();
            $table->string('postback_message')->after('return_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->dropColumn('date_canceled');
            $table->dropColumn('date_rejected');
            $table->dropColumn('postback_message');
        });
    }
}
