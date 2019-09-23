<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableNotazzInvoicesAddDatesColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->integer('max_attempts')->after('attempts')->default(20);
            $table->dateTime('date_last_attempt')->after('attempts')->nullable();
            $table->dateTime('date_pending')->after('attempts')->nullable();
            $table->dateTime('date_sent')->after('attempts')->nullable();
            $table->dateTime('date_completed')->after('attempts')->nullable();
            $table->dateTime('date_error')->after('attempts')->nullable();
            $table->text('return_message')->after('attempts')->nullable();
            $table->integer('return_http_code')->after('attempts')->nullable();
            $table->text('data_json')->after('attempts')->nullable();
            $table->string('notazz_status')->after('attempts')->nullable();
            $table->string('logistic_id')->after('attempts')->nullable();
            $table->text('pdf')->after('attempts')->nullable();
            $table->text('xml')->after('attempts')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->dropColumn('xml');
            $table->dropColumn('pdf');
            $table->dropColumn('logistic_id');
            $table->dropColumn('notazz_status');
            $table->dropColumn('data_json');
            $table->dropColumn('return_http_code');
            $table->dropColumn('return_message');
            $table->dropColumn('date_error');
            $table->dropColumn('date_completed');
            $table->dropColumn('date_sent');
            $table->dropColumn('date_pending');
            $table->dropColumn('date_last_attempt');
            $table->dropColumn('max_attempts');
        });
    }
}
