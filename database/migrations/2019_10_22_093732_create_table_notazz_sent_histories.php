<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNotazzSentHistories extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('notazz_sent_histories', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notazz_invoice_id')->index();
            $table->tinyInteger('sent_type_enum');
            $table->string('url');
            $table->text('data_sent')->nullable();
            $table->text('response')->nullable();

            $table->timestamps();
        });

        Schema::table('notazz_sent_histories', function(Blueprint $table) {
            $table->foreign('notazz_invoice_id')->references('id')->on('notazz_invoices');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notazz_sent_histories');
    }
}
