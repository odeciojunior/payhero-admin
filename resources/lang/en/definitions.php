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
            "pending" => "Pendente",
            "analyzing" => "Em análise",
            "approved" => "Aprovado",
            "refused" => "Recusado",
        ],
        "address_document_status" => [
            "pending" => "Pendente",
            "analyzing" => "Em análise",
            "approved" => "Aprovado",
            "refused" => "Recusado",
        ],
        "contract_document_status" => [
            "pending" => "Pendente",
            "analyzing" => "Em análise",
            "approved" => "Aprovado",
            "refused" => "Recusado",
        ],
        "bank_document_status" => [
            "pending" => "Pendente",
            "analyzing" => "Em análise",
            "approved" => "Aprovado",
            "refused" => "Recusado",
        ],
        "status" => [
            "pending" => "Pendente",
            "analyzing" => "Em análise",
            "approved" => "Aprovado",
            "refused" => "Recusado",
        ],
        "status_affiliate" => [
            "active" => "Ativo",
            "disabled" => "Desativado",
        ],
        "pixel" => [
            "status" => [
                "active" => "Ativo",
                "disabled" => "Desativado",
            ],
        ],
        "discount_coupon" => [
            "status" => [
                "active" => "Ativo",
                "disabled" => "Desativado",
            ],
        ],
        "shipping" => [
            "status" => [
                "active" => "Ativo",
                "disabled" => "Desativado",
            ],
            "pre_selected" => [
                "yes" => "Sim",
                "no" => "Não",
            ],
            "type" => [
                'static' => 'Estático',
                'sedex' => 'SEDEX - Calculado automaticamente',
                'pac' => 'PAC - Calculado automaticamente',
            ],
        ],
        "plan" => [
            "status" => [
                "active" => "Ativo",
                "disabled" => "Desativado",
            ],
        ],
        "withdrawals" => [
            "status" => [
                "pending" => "Pendente",
                "approved" => "Aprovado",
                "transfered" => "Transferido",
                "refused" => "Recusado",
                'in_review' => "Em Revisão",
                'processing' => "Processando",
                'returned' => "Retornado",
                'liquidating' => "Liquidando",
                'partially_liquidated' => "Parcialmente Liquidado",
            ],
        ],
        "invitation" => [
            "status" => [
                "accepted" => "Aceito",
                "pending" => "Pendente",
                "expired" => "Expirado",
            ],
        ],
        "checkout" => [
            "status" => [
                'accessed' => 'Acessado',
                'abandoned cart' => 'Não recuperado',
                'recovered' => 'Recuperado',
                'sale finalized' => 'Venda finalizada',
            ],
        ],
        "sale" => [
            "status" => [
                "approved" => "Aprovado",
                "canceled" => "Cancelado",
                "charge_back" => "Chargeback",
                'in_proccess' => "Em análise",
                "pending" => "Pendente",
                "refused" => "Recusado",
                "refunded" => "Estornado",
                "partial_refunded" => "Estorno Parcial",
                "in_review" => "Revisão Antifraude",
                "system_error" => "Erro de sistema",
                "billet_refunded" => "Estornado",
                "chargeback_recovered" => "Recuperado",
                "in_dispute" => "Em disputa",
            ],
        ],
        "product_plan_sale" => [
            "tracking_status_enum" => [
                "posted" => "Postado",
                "dispatched" => "Em trânsito",
                "delivered" => "Entregue",
                "out_for_delivery" => "Saiu para entrega",
                "exception" => "Problema na entrega",
            ],
        ],
        "tracking" => [
            "tracking_status_enum" => [
                "posted" => "Postado",
                "dispatched" => "Em trânsito",
                "delivered" => "Entregue",
                "out_for_delivery" => "Saiu para entrega",
                "exception" => "Problema na entrega",
            ],
        ],
        "ticket" => [
            "category" => [
                "complaint" => "Reclamação",
                "doubt" => "Dúvida",
                "suggestion" => "Sugestão",

            ],
            "subject" => [
                "differs_from_advertised" => "Produto difere do anunciado",
                "damaged_by_transport" => "Produto danificado pelo transporte",
                "manufacturing_defect" => "Produto não funciona (defeito de fábrica)",
                "tracking_code_not_received" => "Não recebi o código de rastreio",
                "non_trackable_order" => "Não consigo rastrear meu pedido",
                "delivery delay" => "Demora na entrega",
                "delivery_to_wrong_address" => "Entrega no endereço errado",
                "others" => 'Outros',
            ],
            "status" => [
                "open" => "Aberto",
                "closed" => "Resolvido",
                "mediation" => "Em mediação",
            ],
        ],
        "invoices" => [
            "status" => [
                "pending" => "Pendente",
                "send" => "Enviado",
                "completed" => "Finalizado",
                'error' => "Erro",
                "in_process" => "Em processamento",
                "error_max_attempts" => "Maximo de tentativas",
                "canceled" => "Cancelado",
                "rejected" => "Rejeitado",
            ],
        ],
        "role" => [
            "account_owner" => "Dono da conta",
            "admin" => "Administrativo",
            "attendance" => "Atendimento",
        ],
        "user_document_type" => [
            "personal_document" => "Documento Pessoal",
            "address_document" => "Comprovante de residência",
        ],
        "company_document_type" => [
            "bank_document_status" => "Documento Bancário",
            "address_document_status" => "Comprovante de endereço empresarial",
            "contract_document_status" => "Contrato social",
        ],
        "country" => [
            "brazil" => "Brasil",
            "portugal" => "Portugal",
            "usa" => "Estados Unidos",
            "germany" => "Alemanha",
            "spain" => "Espanha",
            "france" => "França",
            "italy" => "Itália",
            "chile" => "Chile",
            "unitedkingdom" => "Reino Unido",
        ],
        "currency" => [
            "real" => "R$",
            "dolar" => "$",
            "euro" => "€",
        ],
    ],

];
