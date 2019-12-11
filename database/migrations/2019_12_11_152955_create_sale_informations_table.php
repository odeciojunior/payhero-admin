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
            $table->unsignedInteger("sale_id");
            $table->string("operational_system")->comment("Sistema operacional do usuário");
            $table->string("browser")->comment("Browser usado para fazer a compra");
            $table->string("browser_fingerprint")->comment("Fingerprint do browser utilizado na compra");
            $table->string("ip")->comment("Ip com comprador");
            $table->string("customer_name")->comment("Nome do comprador");
            $table->string("customer_email")->comment("Email do comprador");
            $table->string("customer_phone")->comment("Telefone do comprador");
            $table->unsignedInteger("customer_identification_number")->index()->comment("CPF/CNPJ do comprador");
            $table->string("product_name")->comment("Nome do produto comprado");
            $table->string("transaction_amount")->comment("Valor total da transação");
            $table->string("country")->index()->comment("País informado pelo comprador");
            $table->string("zip_code")->comment("CEP informado pelo comprador");
            $table->string("state")->index()->comment("Estado informado pelo comprador");
            $table->string("city")->index()->comment("Cidade informada pelo comprador");
            $table->string("district")->comment("Bairro informado pelo comprador");
            $table->string("street_name")->comment("Rua informada pelo comprador");
            $table->string("street_number")->index()->comment("Número da casa informada pelo comprador");
            $table->string("card_brand")->index()->comment("Bandeira do cartão");
            $table->unsignedInteger("installments")->index()->comment("Número de parcelas na compra");
            $table->unsignedInteger("first_six_digits")->index()->comment("Primeiros 6 dígitos do cartão");
            $table->unsignedInteger("last_four_digits")->index()->comment("Últimos 4 dígitos do cartoã");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_informations');
    }
}
