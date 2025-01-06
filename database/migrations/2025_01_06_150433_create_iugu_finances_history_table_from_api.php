<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('iugu_finances_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount', 15, 2);
            $table->string('type');
            $table->string('description');
            $table->date('entry_date');
            $table->string('reference')->nullable();
            $table->string('reference_type')->nullable();
            $table->string('account_id')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('invoice_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->integer('amount_cents')->nullable();
            $table->decimal('balance', 15, 2)->nullable();
            $table->integer('balance_cents')->nullable();
            $table->string('customer_ref')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('id_Fatura')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('iugu_finances_history');
    }
};