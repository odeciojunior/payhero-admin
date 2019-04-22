<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriarTabelaUserEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_empresas', function (Blueprint $table) {
           
            $table->increments('id');
            $table->integer('user')->unsigned();
            $table->integer('empresa')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user')->references('id')->on('users');
            $table->foreign('empresa')->references('id')->on('empresas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_empresas');
    }
}
