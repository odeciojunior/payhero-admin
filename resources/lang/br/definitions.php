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
        "status_affiliate"         => [
            "active"   => "Ativo",
            "disabled" => "Desativado",
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

        "discount_coupon"       => [
            "status" => [
                "active"   => "Ativo",
                "disabled" => "Desativado",
            ],
        ],
        "shipping"              => [
            "status"       => [
                "active"   => "Ativo",
                "disabled" => "Desativado",
            ],
            "pre_selected" => [
                "yes" => "Sim",
                "no"  => "Não",
            ],
            "type"         => [
                'static' => 'Estático',
                'sedex'  => 'SEDEX - Calculado automáticamente',
                'pac'    => 'PAC - Calculado automáticamente',
            ],
        ],
        "plan"                  => [
            "status" => [
                "active"   => "Ativo",
                "disabled" => "Desativado",
            ],
        ],
        "sale"                  => [
            "status" => [
                "approved"        => "Aprovado",
                "canceled"        => "Cancelado",
                "charge_back"     => "Chargeback",
                'in_proccess'     => "Em análise",
                "pending"         => "Pendente",
                "refused"         => "Recusado",
                "refunded"        => "Estornado",
                "partial_refund"  => "Estorno Parcial",
                "in_review"       => "Revisão Antifraude",
                "system_error"    => "Erro de sistema",
                "billet_refunded" => "Boleto estornado",
            ],
        ],
        "invoices"              => [
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
        "role"                  => [
            "account_owner" => "Dono da conta",
            "admin"         => "Administrativo",
            "attendance"    => "Atendimento",
        ],
        "user_document_type"    => [
            "personal_document" => "Documento Pessoal",
            "address_document"  => "Comprovante de residência",
        ],
        "company_document_type" => [
            "bank_document_status"     => "Bancário",
            "address_document_status"  => "Comprovante de endereço empresarial",
            "contract_document_status" => "Contrato social",
        ],
        "country"               => [
            "brazil"        => "Brasil",
            "portugal"      => "Portugal",
            "usa"           => "Estados Unidos",
            "germany"       => "Alemanha",
            "spain"         => "Espanha",
            "france"        => "França",
            "italy"         => "Itália",
            "chile"         => "Chile",
            "unitedkingdom" => "Reino Unido",
        ],
        "currency"              => [
            "real"  => "R$",
            "dolar" => "$",
            "euro"  => "€",
        ],
    ],

];
