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
        "address_document_status"  => [
            "pending"   => "Pendente",
            "analyzing" => "Em análise",
            "approved"  => "Aprovado",
            "refused"   => "Recusado",
        ],
        "contract_document_status" => [
            "pending"   => "Pendente",
            "analyzing" => "Em análise",
            "approved"  => "Aprovado",
            "refused"   => "Recusado",
        ],
        "bank_document_status"     => [
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
        "sale"                     => [
            "status" => [
                "pending"      => "Pendente",
                "charge_back"  => "Chargeback",
                "approved"     => "Aprovado",
                "refused"      => "Recusado",
                "canceled"     => "Cancelado",
                "system_error" => "Erro de sistema",
            ],
        ],
    ],

];
