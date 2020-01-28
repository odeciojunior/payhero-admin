<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNotazzInvoiceTableAddReturnStatusColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->string('return_status')->after('return_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->dropColumn('return_status');
        });
    }
}
