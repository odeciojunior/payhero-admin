<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleInformationsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('sale_informations', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("sale_id")->nullable();
            $table->string("operational_system")->comment("Sistema operacional do usuário")->nullable();
            $table->string("browser")->comment("Browser usado para fazer a compra")->nullable();
            $table->string("browser_fingerprint")->comment("Fingerprint do browser utilizado na compra")->nullable();
            $table->string("browser_token")->comment("Token do browser (salvo no cookie) utilizado na compra")
                  ->nullable();
            $table->string("ip")->comment("Ip com comprador")->nullable()->index();
            $table->string("customer_name")->comment("Nome do comprador")->nullable();
            $table->string("customer_email")->comment("Email do comprador")->nullable();
            $table->string("customer_phone")->comment("Telefone do comprador")->nullable();
            $table->string("customer_identification_number")->comment("CPF/CNPJ do comprador")
                  ->nullable()->index();
            $table->string("project_name")->comment("Nome do projeto")->nullable()->index();
            $table->string("transaction_amount")->comment("Valor total da transação")->nullable()->index();
            $table->string("country")->comment("País informado pelo comprador")->nullable()->index();
            $table->string("zip_code")->comment("CEP informado pelo comprador")->nullable();
            $table->string("state")->comment("Estado informado pelo comprador")->nullable()->index();
            $table->string("city")->comment("Cidade informada pelo comprador")->nullable()->index();
            $table->string("district")->comment("Bairro informado pelo comprador")->nullable();
            $table->string("street_name")->comment("Rua informada pelo comprador")->nullable();
            $table->string("street_number")->comment("Número da casa informada pelo comprador")->nullable()->index();
            $table->string("card_token")->comment("Token do cartão")->nullable()->index();
            $table->string("card_brand")->comment("Bandeira do cartão")->nullable()->index();
            $table->unsignedInteger("installments")->comment("Número de parcelas na compra")->nullable()->index();
            $table->unsignedInteger("first_six_digits")->comment("Primeiros 6 dígitos do cartão")->nullable()->index();
            $table->unsignedInteger("last_four_digits")->comment("Últimos 4 dígitos do cartão")->nullable()->index();
            $table->timestamps();
        });

        Schema::table('sale_informations', function(Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('sale_informations', function(Blueprint $table) {
            $table->dropForeign(["sale_id"]);
        });

        Schema::dropIfExists('sale_informations');
    }
}
