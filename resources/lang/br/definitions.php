<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Definitions Language Lines
    |--------------------------------------------------------------------------
    | Definicoes de cada campo ao se traduzir para ser utilizado nas views
    |
    */
    'enum' => [
        "personal_document_status" => [
            "pending"   => "Pendente",
            "analyzing" => "Em análise",
            "approved"  => "Aprovado",
            "refused"   => "Recusado",
        ],
        "status"                   => [
            "pending"   => "Pendente",
            "analyzing" => "Em análise",
            "approved"  => "Aprovado",
            "refused"   => "Recusado",

        ],
        "pixel"                    => [
            "status" => [
                "active"   => "Ativo",
                "disabled" => "Desativo",
            ],
        ],
        "domain"                   => [
            "status" => [
                "pending"   => "Pendente",
                "analyzing" => "Analizando",
                "approved"  => "Aprovado",
                "refused"   => "Recusado",
            ],
        ],
        "discount_coupon"          => [
            "status" => [
                "active"   => "Ativo",
                "disabled" => "Desativo",
            ],
        ],
        "shipping"                 => [
            "status"       => [
                "active"   => "Ativo",
                "disabled" => "Desativo",
            ],
            "pre_selected" => [
                "yes" => "Sim",
                "no"  => "Não",
            ],
        ],
        "plan"                     => [
            "status" => [
                "active"   => "Ativo",
                "disabled" => "Desativo",
            ],
        ],
    ],

];
