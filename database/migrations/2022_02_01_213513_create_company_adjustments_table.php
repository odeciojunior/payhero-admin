<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('company_adjustments');

        Schema::create('company_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("company_id");
            $table->foreign('company_id')->references('id')->on('companies');
            $table->bigInteger('adjustment_id');
            $table->string('adjustment_amount');
            $table->char('transaction_sign', 1)->nullable();
            $table->char('adjustment_type', 2)->nullable();
            $table->string('adjustment_amount_total');
            $table->string('adjustment_reason');
            $table->dateTime('date_adjustment');
            $table->dateTime('subseller_rate_closing_date')->nullable();
            $table->json('data')->nullable();
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
        Schema::dropIfExists('company_adjustments');
    }
}
