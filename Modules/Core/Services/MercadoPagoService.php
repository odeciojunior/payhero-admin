<?php

namespace Modules\Core\Services;

class MercadoPagoService
{
    static function getErrorMessage($errorCode)
    {
        $errors = [
            '1'    => 'Erro nos parametros',
            '3'    => 'Token deve ser para teste',
            '5'    => 'Deve fornecer seu access_token para continuar',
            '1000' => 'Numero de linhas excedeu os limites',
            '1001' => 'Formato da data deve ser yyyy-MM-dd"T"HH:mm:ss.SSSZ',
            '2001' => 'Já postou o mesmo pedido no último minuto',
            '2004' => 'Falha na API POST do gateway transacões ',
            '2002' => 'Cliente não encontrado',
            '2006' => 'token do cartão não encontrado',
            '2007' => 'Falha na conexão com a API do token do cartão',
            '2009' => 'O token do cartão não pode ser nulo',
            '2060' => 'O cliente não pode ser igual ao collector',
            '3000' => 'Você deve fornecer seu nome do titular do cartão com os dados do seu cartão',
            '3001' => 'Você deve fornecer seu cardissuer_id com os dados do seu cartão',
            '3003' => 'card_token_id inválido',
            '3004' => 'Parâmetro inválido site_id',
            '3005' => 'Ação inválida, o recurso está em um estado que não permite essa operação. Para mais informações, consulte o estado que possui o recurso',
            '3006' => 'Parâmetro inválido cardtoken_id',
            '3007' => 'O parâmetro client_id não pode ser nulo nem vazio',
            '3008' => 'Não encontrado Cardtoken',
            '3009' => 'client_id não autorizado',
            '3010' => 'Não foi encontrado cartão na lista de permissões',
            '3011' => 'Parâmetro payment_method não encontrado',
            '3012' => 'Parâmetro inválido security_code_length',
            '3013' => 'O parâmetro security_code é um campo obrigatório e não pode ser nulo ou vazio',
            '3014' => 'Parâmetro inválido payment_method',
            '3015' => 'Parâmetro inválido card_number_length',
            '3016' => 'Parâmetro inválido card_number',
            '3017' => 'O parâmetro card_number_id não pode ser nulo ou vazio',
            '3018' => 'O parâmetro expiration_month não pode ser nulo ou vazio',
            '3019' => 'O parâmetro expiration_year não pode ser nulo ou vazio',
            '3020' => 'O parâmetro cardholder.name não pode ser nulo ou vazio',
            '3021' => 'O parâmetro cardholder.document.number não pode ser nulo ou vazio',
            '3022' => 'O parâmetro cardholder.document.type não pode ser nulo ou vazio',
            '3023' => 'O parâmetro cardholder.document.subtype não pode ser nulo ou vazio',
            '3024' => 'Ação inválida - reembolso parcial não suportado para esta transação',
            '3025' => 'Códido de autenticação inválido',
            '3026' => 'card_id inválido para este payment_method_id',
            '3027' => 'payment_type_id inválido',
            '3028' => 'payment_method_id inválido',
            '3029' => 'Mês de expiração do cartão inválido',
            '3030' => 'Ano de expiração do cartão inválido',
            '4000' => 'card não pode ser nulo',
            '4001' => 'payment_method_id não pode ser nulo',
            '4002' => 'transaction_amount não pode ser nulo',
            '4003' => 'transaction_amount deve ser um numérico',
            '4004' => 'installments não pode ser nulo',
            '4005' => 'installments deve ser um numérico',
            '4006' => 'payer atribute está malformatado',
            '4007' => 'site_id não pode ser nulo',
            '4012' => 'payer.id não pode ser nulo',
            '4013' => 'payer.type não pode ser nulo',
            '4015' => 'payment_method_reference_id não pode ser nulo',
            '4016' => 'payment_method_reference_id deve ser um numérico',
            '4017' => 'status não pode ser nulo',
            '4018' => 'payment_id não pode ser nulo',
            '4019' => 'payment_id deve ser um numérico',
            '4020' => 'notificaction_url deve ser uma url válida',
            '4021' => 'notificaction_url deve ter menos de 500 caracteres',
            '4022' => 'metadata deve ser um JSON válido',
            '4023' => 'transaction_amount não pode ser nulo',
            '4024' => 'transaction_amount deve ser um numérico',
            '4025' => 'refund_id não pode ser nulo',
            '4026' => 'coupon_amount inválido',
            '4027' => 'campaign_id deve ser numérico',
            '4028' => 'coupon_amount deve ser numérico',
            '4029' => 'Tipo de pagador inválido',
            '4037' => 'Valor da transação inválido',
            '4038' => 'A taxa não pode ser maior que o valor da compra',
            '4039' => 'A taxa não pode ser um valor negativo',
            '4050' => 'Email do cliente deve se um email valido',
            '4051' => 'Email do cliente deve ter menos de 254 caracteres',
        ];

        if (array_key_exists($errorCode, $errors)) {
            return $errors[$errorCode];
        } else {
            return 'OCORREU ALGUM ERRO, TENTE NOVAMENTE EM ALGUNS MINUTOS!';
        }
    }

    static function getChangeErrorMessage(string $stringError)
    {
        $errors = [
            'cc_rejected_bad_filled_card_number'   => 'Confira o número do cartão',
            'cc_rejected_bad_filled_date'          => 'Confira a data de validade',
            'cc_rejected_bad_filled_other'         => 'Confira os dados',
            'cc_rejected_bad_filled_security_code' => 'Confira o código de segurança',
            'cc_rejected_blacklist'                => 'Não cosneguimos processar seu pagamaento',
            'cc_rejected_call_for_authorize'       => 'Você deve autorizar o pagamento',
            'cc_rejected_card_disabled'            => 'Ligue para a central do seu cartão para autorizar a compra',
            'cc_rejected_card_error'               => 'Não conseguimos processar seu pagamento',
            'cc_rejected_duplicated_payment'       => 'Você já efetuou um pagamento com esse valor. Caso precise pagar novamente, utilize outro cartão ou outra forma de pagamento',
            'cc_rejected_high_risk'                => 'Seu pagamento foi recusado. Escolha outra forma de pagamento. Recomendamos meios de pagamento em dinheiro',
            'cc_rejected_insufficient_amount'      => 'O cartão possui saldo insuficiente',
            'cc_rejected_invalid_installments'     => 'O cartão não processa pagamentos parcelados',
            'cc_rejected_max_attempts'             => 'Você atingiu o limite de tentativas permitido. Escolha outro  cartão ou outra forma de pagamento',
            'cc_rejected_other_reason'             => 'Ocorreu algum erro, tente novamente em alguns minutos',
        ];
        if (array_key_exists($stringError, $errors)) {
            return $errors[$stringError];
        } else {
            return 'Ocorreu algum erro, tente novamente em alguns minutos!';
        }
    }
}

