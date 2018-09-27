<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterarTabelaUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('data_nascimento')->nullable();
            $table->string('celular')->nullable();
            $table->string('cpf')->nullable();
            $table->string('cep')->nullable();
            $table->char('pais', 2)->nullable();
            $table->string('estado')->nullable();
            $table->string('cidade')->nullable();
            $table->string('bairro')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('telefone2')->nullable();
            $table->string('telefone1')->nullable();
            $table->string('referencia')->nullable();
            $table->string('foto')->nullable();
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('data_nascimento');
            $table->dropColumn('celular');
            $table->dropColumn('cpf');
            $table->dropColumn('cep');
            $table->dropColumn('pais');
            $table->dropColumn('estado');
            $table->dropColumn('cidade');
            $table->dropColumn('bairro');
            $table->dropColumn('logadouro');
            $table->dropColumn('numero');
            $table->dropColumn('complemento');
            $table->dropColumn('telefone2');
            $table->dropColumn('telefone1');
            $table->dropColumn('referencia');
            $table->dropColumn('foto');
        });
    }
}
