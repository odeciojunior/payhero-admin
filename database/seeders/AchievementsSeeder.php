<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\Achievement;

class AchievementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->getJsonAchievement() as $item) {
            Achievement::create($item);
        }
    }

    public function getJsonAchievement()
    {
        return json_decode(
            '[
                {
                    "name": "Velocidade da Luz",
                    "description": "Tenha nota 9 ou maior nos Rastreios",
                    "storytelling": "Apenas os vendedores com as entregas mais rápidas da galáxia ostentam esta medalha. Você deve efetuar suas entregas a bordo da USS Enterprise! Continue fazendo um ótimo trabalho.",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/VelocidadeLuz.png"
                },
                {
                    "name": "Suporte Meteórico",
                    "description": "Tenha um tempo médio de resposta de 12h",
                    "storytelling": "Problemas sempre acontecem, isso não é novidade alguma. Mas a sua velocidade em resolver cada um deles é surpreendente, você é digno de receber nossa honraria de Suporte Meteórico!",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/SuporteMeteorico.png"
                },
                {
                    "name": "Colonizador",
                    "description": "Tenha 10 convites aprovados",
                    "storytelling": "Espalhar notícias faz parte da natureza do viajante, mas você está se tornando um colonizador cósmico e já conseguiu mais de 10 convites aprovados em sua jornada. Você é uma lenda!",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/Colonizador.png"
                },
                {
                    "name": "Comerciante Celeste",
                    "description": "Tenha 1000 vendas aprovadas",
                    "storytelling": "Apenas os Comerciantes Celestes, que dominam todas as regiões espaciais conseguem o feito de ter mais de 1000 vendas aprovadas. O céu é o limite... Não para você!",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/ComercianteCeleste.png"
                },
                {
                    "name": "Poeira Estelar",
                    "description": "Tenha 100 vendas digitais aprovadas",
                    "storytelling": "Ao ultrapassar a marca de 100 vendas digitais aprovadas você prova que domina o necessário para cruzar o espaço. Aqui está a sua medalha de Poeira Estelar: seu sucesso está deixando rastros!",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/PoeiraEstelar.png"
                },
                {
                    "name": "Estrela Cadente",
                    "description": "Tenha 10 afiliados ativos",
                    "storytelling": "Dizem que você dá sorte, mas na verdade, você dá oportunidades! Você recebe esta medalha ao recrutar 10 afiliados ativos em sua jornada. Até vender é mais divertido quando estamos acompanhados!",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/estrelaCadente.png"
                },
                {
                    "name": "Guerra nas Estrelas",
                    "description": "Tenha 50% de boletos compensados (+100)",
                    "storytelling": "Um verdadeiro Jedi das conversões de boleto. Sábio, rápido, conhecedor das técnicas de vendas. Na galáxia dos negócios, boleto bom é boleto convertido e você teve 50% dos boletos aprovados!",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/GuerraNasEstrelas.png"
                },
                {
                    "name": "Extraterrestre",
                    "description": "Recupere 6% dos carrinhos abandonados",
                    "storytelling": "Carrinhos abandonados, identificados e recuperados com sucesso. Ao converter 6% dos carrinhos você adquire a medalha de Extraterrestre e prova que existe vida inteligente lá fora.",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/Extraterrestre.png"
                },
                {
                    "name": "Mochileiro das Galáxias",
                    "description": "Tenha vendas em 5 lojas diferentes",
                    "storytelling": "Se existe um Guia do Mochileiro das Galáxias provavelmente é você! Conhece cada canto, de todos os lugares e ao vender em 5 projetos diferentes você se torna um Mochileiro das Galáxias credenciado!",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/MochileiroGalaxias.png"
                },
                {
                    "name": "Órbita Capitalista",
                    "description": "Realize 50 saques",
                    "storytelling": "O capitalismo é o sistema econômico de todo o universo, e ao realizar 50 saques aqui na plataforma você prova que domina os negócios em qualquer lugar do espaço, um verdadeiro conquistador!",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/OrbitaCapitalista.png"
                },
                {
                    "name": "Lunático",
                    "description": "Faça login por 21 dias consecutivos",
                    "storytelling": "Uma notícia boa e uma outra ótima: a boa é que você acaba de ganhar a medalha de Lunático por fazer login durante 21 dias. A ótima é que os lunáticos conseguem tudo o que querem!",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/Lunatico.png"
                },
                {
                    "name": "Ao Infinito e Além",
                    "description": "Faça 100 vendas com orderbump ou upsell",
                    "storytelling": "Esta medalha rara é uma honraria do Comando Estelar para os valentes que conseguem fazer 100 vendas com Order Bump ou Upsell. Seria este o segredo do Buzz Lightyear? Só você poderá dizer!",
                    "icon": "https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/AoInfinioAlem.png"
                }
            ]',
            true
        );
    }
}
