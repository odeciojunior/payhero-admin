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
            // Alterar os valores default das colunas
            $table->string('transaction_tax', 255)->default('1.50')->collation('utf8mb4_unicode_ci')->change();
            $table->string('installment_tax', 255)->default('4.99')->collation('utf8mb4_unicode_ci')->change();
        });

        // Atualizar os registros existentes
        DB::statement("UPDATE companies SET installment_tax = '4.99'");
        DB::statement("UPDATE users SET contestation_penalty_tax = '6000'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            // Reverter os valores default das colunas
            $table->string('transaction_tax', 255)->default('1.00')->collation('utf8mb4_unicode_ci')->change();
            $table->string('installment_tax', 255)->default('2.99')->collation('utf8mb4_unicode_ci')->change();
        });

        // Reverter a atualização dos registros
        DB::statement("UPDATE companies SET installment_tax = '2.99'");
        DB::statement("UPDATE users SET contestation_penalty_tax = '5000'");
    }
};
