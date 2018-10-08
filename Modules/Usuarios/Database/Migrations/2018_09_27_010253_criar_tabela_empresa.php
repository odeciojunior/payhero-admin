<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriarTabelaEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status')->nullable();
            $table->string('emaill')->nullable();
            $table->string('cep')->nullable();
            $table->char('municipio', 2)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('cod_atividade')->nullable();
            $table->string('data_situacao')->nullable();
            $table->string('situacao')->nullable();
            $table->string('abertura')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('numero')->nullable();
            $table->string('ultima_atualizacao')->nullable();
            $table->string('fantasia')->nullable();
            $table->string('capital_social')->nullable();
            $table->string('atividade_principal')->nullable();
            $table->string('nome')->nullable();
            $table->string('uf')->nullable();
            $table->string('telefone')->nullable();
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
        Schema::dropIfExists('empresas');
    }
}
