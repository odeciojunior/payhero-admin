<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableSalesChangeBoletoDueDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("update sales set boleto_due_date = SUBSTRING(boleto_due_date, 1, 10)
                                where boleto_due_date is not null
                                and length(boleto_due_date) > 10");

        DB::statement("alter table sales modify boleto_due_date timestamp default null null");

        Schema::table('sales', function(Blueprint $table) {
            $table->index('boleto_due_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->dropIndex(['boleto_due_date']);
        });
    }
}
