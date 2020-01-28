<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuotation extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('currency_quotations', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('currency');
            $table->unsignedTinyInteger('currency_type')->default(1); //1 - BRL, 2 - USD
            $table->text('http_response')->nullable();
            $table->string('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_quotations');
    }
}
