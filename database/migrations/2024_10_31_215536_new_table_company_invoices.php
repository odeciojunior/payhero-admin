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
        // Deleta a tabela se ela já existir e estiver vazia
        if (Schema::hasTable('company_invoices') && DB::table('company_invoices')->count() == 0) {
            Schema::drop('company_invoices');

            Schema::table('companies', function (Blueprint $table) {
                $table->index('document');
            });
    
            // Cria a tabela
            Schema::create('company_invoices', function (Blueprint $table) {
                $table->bigIncrements("id");
    
                // Definindo a coluna e chave estrangeira para company_id
                $table->unsignedInteger("company_id")->nullable();
                $table->foreign("company_id")
                      ->references("id")
                      ->on("companies")
                      ->onDelete("set null");
    
                // Definindo a coluna e chave estrangeira para document
                $table->string("document")->nullable();
                $table->foreign("document")
                      ->references("document")
                      ->on("companies")
                      ->onDelete("set null");
    
                // Demais colunas
                $table->string("invoice_id", 100)->nullable();
                $table->string("value")->nullable();
                $table->string("description")->nullable();
                $table->string("tax_iss")->nullable();
                $table->string("invoice_date")->nullable();
                $table->integer('status')->nullable();
                $table->string("notazz_id")->nullable();
    
                // Timestamps e Soft Deletes
                $table->timestamps();
                $table->softDeletes(); // Corrige o campo deleted_at para soft delete
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('company_invoices')) {
            Schema::dropIfExists('company_invoices');
        }
    }
};
