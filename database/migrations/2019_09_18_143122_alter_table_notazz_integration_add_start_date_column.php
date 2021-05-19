<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableNotazzIntegrationAddStartDateColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('notazz_integrations', function(Blueprint $table) {
            $table->dateTime('start_date')->after('token_logistics')->nullable();
            $table->tinyInteger('invoice_type')->after('token_logistics')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('notazz_integrations', function(Blueprint $table) {
            $table->dropColumn('invoice_type');
            $table->dropColumn('start_date');
        });
    }
}
