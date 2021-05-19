<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNotazzInvoices extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('notazz_invoices', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('notazz_integration_id');
            $table->tinyInteger('invoice_type')->default(1); //1 - servico, 2 - produto fisico
            $table->text('notazz_id')->nullable();
            $table->string('external_id')->nullable();
            $table->tinyInteger('status'); //1- pending, 2- send, 3- completed, 5- error
            $table->tinyInteger('canceled_flag')->index(0); //0 - false, 1- true
            $table->dateTime('schedule');
            $table->integer('attempts')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales');
        });

        Schema::table('notazz_invoices', function(Blueprint $table) {
            $table->foreign('notazz_integration_id')->references('id')->on('notazz_integrations');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notazz_invoices');
    }
}
