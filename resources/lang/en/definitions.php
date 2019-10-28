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
                "active"   => "Ativo",
                "disabled" => "Desativado",
            ],
        ],
        "discount_coupon"          => [
            "status" => [
                "active"   => "Ativo",
                "disabled" => "Desativado",
            ],
        ],
        "shipping"                 => [
            "status"       => [
                "active"   => "Ativo",
                "disabled" => "Desativado",
            ],
            "pre_selected" => [
                "yes" => "Sim",
                "no"  => "Não",
            ],
        ],
        "plan"                     => [
            "status" => [
                "active"   => "Ativo",
                "disabled" => "Desativado",
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
                'in_proccess'  => "Em análise",
                "pending"      => "Pendente",
                "refused"      => "Recusado",
                "system_error" => "Erro de sistema",
            ],
        ],
        "product_plan_sale"        => [
            "tracking_status_enum" => [
                "posted"           => "Postado",
                "dispatched"       => "Despachado",
                "delivered"        => "Entregue",
                "out_for_delivery" => "Saiu para entrega",
                "exception"        => "Problema na entrega"
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
    ],

];
