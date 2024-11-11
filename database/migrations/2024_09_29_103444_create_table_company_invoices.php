<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_invoices', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")
                    ->nullable()
                    ->foreign("company_id")
                    ->references("id")
                    ->on("companies");
            $table->string("document")
                    ->nullable()
                    ->foreign("document")
                    ->references("document")
                    ->on("companies");
            // $table->string("document", 100)->nullable();
            $table->string("invoice_id", 100)->nullable();
            $table->string("value")->nullable();
            $table->string("description")->nullable();
            $table->string("tax_iss")->nullable();
            $table->string("invoice_date")->nullable();
            $table->integer('status')->nullable();
            $table->string("notazz_id")->nullable();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_invoices');
    }
};
