<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'pending_debts',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->nullable();
                $table->foreignId('withdrawal_id')->nullable();
                $table->foreignId('company_id')->nullable();
                $table->integer('value')->nullable();
                $table->string('reason')->nullable();
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_debts');
    }
}
