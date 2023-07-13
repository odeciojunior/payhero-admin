<?php

namespace Modules\Core\Services\Performance;

/**
 * Class UserLevel
 * @package Modules\Core\Services\Performance
 */
class UserLevel
{
    public const LEVEL_DATA = [
        "1" => [
            "name" => "Aventureiro",
            "description" => "Pronto para começar?",
            "storytelling" =>
                "Nossa jornada está apenas começando. Você já pode começar a olhar o céu noturno e se imaginar navegando na imensidão do desconhecido, é hora de mirar as estrelas e se preparar para a maior aventura de sua vida empreendedora.",
            "icon" => "https://azcend-digital-products.s3.amazonaws.com/admin/admin-002/nivel-1.png",
        ],
        "2" => [
            "name" => "Viajante Espacial",
            "description" => "Nível 2",
            "storytelling" =>
                "Nosso foguete está saindo da Terra, este momento de fortes emoções foi experimentado por poucos! Quem diria, de tanto olhar para o céu estrelado, hoje você está navegando por ele, rumo à nossa primeira parada: a lua!",
            "icon" => "https://azcend-digital-products.s3.amazonaws.com/admin/admin-002/nivel-2.png",
        ],
        "3" => [
            "name" => "Conquistador",
            "description" => "Nível 3",
            "storytelling" =>
                "Nível 3? Você está avançando bem, daqui da lua você já consegue enxergar que a Terra é pequena demais para você. Aproveite a vista, faça pequenos reparos porque ainda temos bastante aventura pela frente e a próxima parada é Marte!",
            "icon" => "https://azcend-digital-products.s3.amazonaws.com/admin/admin-002/nivel-3.png",
        ],
        "4" => [
            "name" => "Colonizador",
            "description" => "Nível 4",
            "storytelling" =>
                "Elon Musk ficaria orgulhoso, pisar em Marte é para poucos, seja na vida real ou até mesmo no nosso game. 10 milhões de faturamento te coloca na mais alta patente, com os mais destemidos empreendedores da galáxia!",
            "icon" => "https://azcend-digital-products.s3.amazonaws.com/admin/admin-002/nivel-4.png",
        ],
        "5" => [
            "name" => "Capitão Galáctico",
            "description" => "Nível 5",
            "storytelling" =>
                "Existe vida fora da Terra e agora você é capaz de provar. Apesar de estarmos bem longe, nossa viagem deve continuar, mas se fosse para ficar... os nativos ficariam orgulhosos com sua história, de onde você veio e para onde está indo!",
            "icon" => "https://azcend-digital-products.s3.amazonaws.com/admin/admin-002/nivel-5.png",
        ],
        "6" => [
            "name" => "Admin Major",
            "description" => "Nível 6",
            "storytelling" =>
                "Parabéns! Você atingiu os confins do universo e a expressiva marca de 100M de faturamento, um verdadeiro explorador do espaço e dos negócios. Você acaba de chegar na Canis Major e conhecer de perto a Admin, a estrela mais brilhante!",
            "icon" => "https://azcend-digital-products.s3.amazonaws.com/admin/admin-002/nivel-6.png",
        ],
    ];

    public function getLevelData(int $level): array
    {
        return self::LEVEL_DATA[$level];
    }
}
