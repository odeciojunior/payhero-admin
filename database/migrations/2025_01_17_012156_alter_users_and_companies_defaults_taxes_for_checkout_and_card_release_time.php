<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->time('credit_card_release_time')->default('11:00:00')->change();
            $table->decimal('transaction_tax', 8, 2)->default(3)->change();
            $table->decimal('checkout_tax', 8, 2)->default(0.5)->change();
            $table->integer('credit_card_release_money_days')->default(3)->change();
            
        });



        // Update all existing records to have the new default value
        DB::table('companies')->update([
                'credit_card_release_time' => '11:00:00',
                'transaction_tax' => 3,
                'checkout_tax' => 0.5,
                'credit_card_release_money_days' => 3
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->time('credit_card_release_time')->default('11:00:00')->change();
            $table->decimal('transaction_tax', 8, 2)->default(2.5)->change();
            $table->integer('credit_card_release_money_days')->default(2)->change();
        });
    }
};
