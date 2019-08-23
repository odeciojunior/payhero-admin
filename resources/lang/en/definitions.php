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
        "pixel"                    => [
            "status" => [
                "active"   => "ativo",
                "disabled" => "desativo",
            ],
        ],
        "discount_coupon"          => [
            "status" => [
                "active"   => "ativo",
                "disabled" => "desativo",
            ],
        ],
        "shipping"                 => [
            "status"       => [
                "active"   => "ativo",
                "disabled" => "desativo",
            ],
            "pre_selected" => [
                "yes" => "sim",
                "no"  => "não",
            ],
        ],
        "plan"                     => [
            "status" => [
                "active"   => "ativo",
                "disabled" => "desativo",
            ],
        ],
        "withdrawals"              => [
            "status" => [
                "pending"    => "Pendente",
                "approved"   => "Aprovado",
                "transfered" => "Transferido",
                "refused"    => "Recusado",
            ],
        ],
        "invitation"               => [
            "status" => [
                "accepted" => "Aceito",
                "pending"  => "Pendente",
                "expired"  => "Expirado",
            ],
        ],
        "checkout"                 => [
            "status" => [
                'accessed'       => 'Acessado',
                'abandoned cart' => 'Não recuperado',
                'recovered'      => 'Recuperado',
                'sale finalized' => 'Venda finalizada',
            ],
        ],
        "sale"                     => [
            "status" => [
                "approved"     => "Aprovado",
                "canceled"     => "Cancelado",
                "charge_back"  => "Chargeback",
                'in_process'   => "Em análise",
                "pending"      => "Pendente",
                "refused"      => "Recusado",
                "system_error" => "Erro de sistema",
            ],
        ],
    ],

];
