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
        "status"                   => [
            "pending"   => "Pendente",
            "analyzing" => "Em análise",
            "approved"  => "Aprovado",
            "refused"   => "Recusado",

        ],
        "pixel"                    => [
            "status" => [
                "active"   => "Ativo",
                "disabled" => "Desativado",
            ],
        ],
        "domain"                   => [
            "status" => [
                "pending"   => "Pendente",
                "analyzing" => "Em análise",
                "approved"  => "Aprovado",
                "refused"   => "Recusado",

            ],
        ],

        "discount_coupon" => [
            "status" => [
                "active"   => "Ativo",
                "disabled" => "Desativado",
            ],
        ],
        "shipping"        => [
            "status"       => [
                "active"   => "Ativo",
                "disabled" => "Desativado",
            ],
            "pre_selected" => [
                "yes" => "Sim",
                "no"  => "Não",
            ],
        ],
        "plan"            => [
            "status" => [
                "active"   => "Ativo",
                "disabled" => "Desativado",
            ],
        ],
        "sale"            => [
            "status" => [
                "approved"     => "Aprovado",
                "canceled"     => "Cancelado",
                "charge_back"  => "Chargeback",
                'in_proccess'  => "Em análise",
                "pending"      => "Pendente",
                "refused"      => "Recusado",
                "system_error" => "Erro de sistema",
            ],
        ],
        "invoices"        => [
            "status" => [
                "pending"            => "Pendente",
                "send"               => "Enviado",
                "completed"          => "Finalizado",
                'error'              => "Erro",
                "in_process"         => "Em processamento",
                "error_max_attempts" => "Maximo de tentativas",
                "canceled"           => "Cancelado",
                "rejected"           => "Rejeitado",
            ],
        ],
        "role"            => [
            "account_owner" => "Dono da conta",
            "admin"         => "Administrativo",
            "attendance"    => "Atendimento",
        ],
    ],

];
