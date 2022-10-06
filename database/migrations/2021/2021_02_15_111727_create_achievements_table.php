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
        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        DB::statement("DROP TABLE IF EXISTS achievements");
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");

        Schema::create("achievements", function (Blueprint $table) {
            $table->id();
            $table->string("name", 100);
            $table->string("description", 100);
            $table->text("storytelling");
            $table->string("icon", 100);
            $table->timestamps();
        });

        DB::statement("INSERT INTO achievements (id, name, description, storytelling, icon, created_at)
                             VALUES ( 1, 'Velocidade da Luz',       'Tenha nota 9 ou maior nos Rastreios',     'Apenas os vendedores com as entregas mais rápidas da galáxia ostentam esta medalha. Você deve efetuar suas entregas a bordo da USS Enterprise! Continue fazendo um ótimo trabalho.', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/VelocidadeLuz.png',                         now()),
                                    ( 2, 'Suporte Meteórico',       'Tenha um tempo médio de resposta de 12h', 'Problemas sempre acontecem, isso não é novidade alguma. Mas a sua velocidade em resolver cada um deles é surpreendente, você é digno de receber nossa honraria de Suporte Meteórico!', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/SuporteMeteorico.png',                    now()),
                                    ( 3, 'Colonizador',             'Tenha 10 convites aprovados',             'Espalhar notícias faz parte da natureza do viajante, mas você está se tornando um colonizador cósmico e já conseguiu mais de 10 convites aprovados em sua jornada. Você é uma lenda!', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/Colonizador.png',                         now()),
                                    ( 4, 'Comerciante Celeste',     'Tenha 1000 vendas aprovadas',             'Apenas os Comerciantes Celestes, que dominam todas as regiões espaciais conseguem o feito de ter mais de 1000 vendas aprovadas. O céu é o limite... Não para você!', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/ComercianteCeleste.png',                          now()),
                                    ( 5, 'Poeira Estelar',          'Tenha 100 vendas digitais aprovadas',     'Ao ultrapassar a marca de 100 vendas digitais aprovadas você prova que domina o necessário para cruzar o espaço. Aqui está a sua medalha de Poeira Estelar: seu sucesso está deixando rastros!', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/PoeiraEstelar.png',             now()),
                                    ( 6, 'Estrela Cadente',         'Tenha 10 afiliados ativos',               'Dizem que você dá sorte, mas na verdade, você dá oportunidades! Você recebe esta medalha ao recrutar 10 afiliados ativos em sua jornada. Até vender é mais divertido quando estamos acompanhados!', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/estrelaCadente.png',         now()),
                                    ( 7, 'Guerra nas Estrelas',     'Tenha 50% de boletos compensados (+100)', 'Um verdadeiro Jedi das conversões de boleto. Sábio, rápido, conhecedor das técnicas de vendas. Na galáxia dos negócios, boleto bom é boleto convertido e você teve 50% dos boletos aprovados!', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/GuerraNasEstrelas.png',          now()),
                                    ( 8, 'Extraterrestre',          'Recupere 6% dos carrinhos abandonados',   'Carrinhos abandonados, identificados e recuperados com sucesso. Ao converter 6% dos carrinhos você adquire a medalha de Extraterrestre e prova que existe vida inteligente lá fora.', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/Extraterrestre.png',                       now()),
                                    ( 9, 'Mochileiro das Galáxias', 'Tenha vendas em 5 projetos diferentes',   'Se existe um Guia do Mochileiro das Galáxias provavelmente é você! Conhece cada canto, de todos os lugares e ao vender em 5 projetos diferentes você se torna um Mochileiro das Galáxias credenciado!', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/MochileiroGalaxias.png', now()),
                                    (10, 'Órbita Capitalista',      'Realize 50 saques',                       'O capitalismo é o sistema econômico de todo o universo, e ao realizar 50 saques aqui na plataforma você prova que domina os negócios em qualquer lugar do espaço, um verdadeiro conquistador!', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/OrbitaCapitalista.png',          now()),
                                    (11, 'Lunático',                'Faça login por 21 dias consecutivos',     'Uma notícia boa e uma outra ótima: a boa é que você acaba de ganhar a medalha de Lunático por fazer login durante 21 dias. A ótima é que os lunáticos conseguem tudo o que querem!', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/Lunatico.png',                              now()),
                                    (12, 'Ao Infinito e Além',      'Faça 100 vendas com orderbump ou upsell', 'Esta medalha rara é uma honraria do Comando Estelar para os valentes que conseguem fazer 100 vendas com Order Bump ou Upsell. Seria este o segredo do Buzz Lightyear? Só você poderá dizer!', 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/AoInfinioAlem.png',                 now());");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("achievements");
    }
}
