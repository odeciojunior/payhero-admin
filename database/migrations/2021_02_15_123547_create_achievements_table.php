<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAchievementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 100);
            $table->string('icon', 100);
            $table->timestamps();
        });

        DB::statement("INSERT INTO achievements (id, name, description, icon, created_at)
                             VALUES (1, 'Velocidade da Luz', 'Faça entregas rápidas', 'SpeedOfLight.png', now()),
                                    (2, 'Suporte Meteórico', 'Tenha um atendimento rápido', 'MeteoricSupport.png', now()),
                                    (3, 'Colonizador', 'Tenha +10 convites aprovados', 'Colonizer.png', now()),
                                    (4, 'Comerciante Celeste', '+1000 vendas aprovadas no cartão', 'SkySeller.png', now()),
                                    (5, 'Poeira Estelar', '100 vendas digitais aprovadas', 'StarDust.png', now()),
                                    (6, 'Estrela Cadente', '10 afiliados ativos', 'FallingStar.png', now()),
                                    (7, 'Guerra nas Estrelas', '50% de boletos aprovados', 'StarWars.png', now()),
                                    (8, 'Extraterrestre', 'Recupere 6% dos carrinhos abandonados', 'Alien.png', now()),
                                    (9, 'Mochileiro das Galáxias', 'Faça vendas em 5 projetos diferentes', 'HitchhikerOfGalaxies.png', now()),
                                    (10, 'Órbita Capitalista', 'Realize 50 saques', 'CapitalistOrbit.png', now()),
                                    (11, 'Lunático', 'Faça login por 21 dias', 'Moonstruck.png', now()),
                                    (12, 'Ao Infinito e Além', 'Faça 50 vendas com Orderbump ou Upsell', 'InfinityAndBeyond.png', now());");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('achievements');
    }
}
