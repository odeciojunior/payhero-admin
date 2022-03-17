<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNotificacoesInteligentesIntegration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_notificacoes_inteligentes_integration', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('link');
            $table->string('token');

            $table->boolean('boleto_generated')->default(true);
            $table->boolean('boleto_paid')->default(true);
            $table->boolean('credit_card_refused')->default(true);
            $table->boolean('credit_card_paid')->default(true);
            $table->boolean('abandoned_cart')->default(true);
            $table->boolean('pix_generated')->default(true);
            $table->boolean('pix_paid')->default(true);
            $table->boolean('pix_expired')->default(true);


            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('table_notificacoes_inteligentes_integration');
    }
}
