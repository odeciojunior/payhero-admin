<?php


namespace Modules\Transfers\Services;

use Carbon\Carbon;
use Exception;
use Modules\Core\Services\FoxUtils;
use stdClass;

class GetNetStatementService
{

    const TRANSACTION_STATUS_CODE_APROVADO = 0;
    const TRANSACTION_STATUS_CODE_AGUARDANDO = 70;
    const TRANSACTION_STATUS_CODE_PENDENTE = 77;
    const TRANSACTION_STATUS_CODE_PENDENTE_PAGAMENTO = 78;
    const TRANSACTION_STATUS_CODE_TIMEOUT = 83;
    const TRANSACTION_STATUS_CODE_DESFEITA = 86;
    const TRANSACTION_STATUS_CODE_INEXISTENTE = 90;
    const TRANSACTION_STATUS_CODE_NEGADO_ADMINISTRADORA = 91;
    const TRANSACTION_STATUS_CODE_ESTORNADA = 92;
    const TRANSACTION_STATUS_CODE_REPETIDA = 93;
    const TRANSACTION_STATUS_CODE_ESTORNADA_CONCILIACAO = 94;
    const TRANSACTION_STATUS_CODE_CANCELADA_SEM_CONFIRMACAO = 98;
    const TRANSACTION_STATUS_CODE_NEGADO_MGM = 99;

    const TRANSACTION_SIGN_MINUS = '-';
    const TRANSACTION_SIGN_MORE = '+';

    const SEARCH_STATUS_ALL = 'ALL';
    const SEARCH_STATUS_WAITING_FOR_VALID_POST = 'WAITING_FOR_VALID_POST';
    const SEARCH_STATUS_WAITING_LIQUIDATION = 'WAITING_LIQUIDATION';
    const SEARCH_STATUS_PAID = 'PAID';
    const SEARCH_STATUS_REVERSED = 'REVERSED';
    const SEARCH_STATUS_UNKNOW = 'UNKNOW';

    private function translateTransactionStatusCode(int $status)
    {

        switch ($status) {

            case self::TRANSACTION_STATUS_CODE_APROVADO:
                return 'Aprovado';
            case self::TRANSACTION_STATUS_CODE_AGUARDANDO:
                return 'Aguardando';
            case self::TRANSACTION_STATUS_CODE_PENDENTE:
                return 'Pendente';
            case self::TRANSACTION_STATUS_CODE_PENDENTE_PAGAMENTO:
                return 'Pendente Pagamento';
            case self::TRANSACTION_STATUS_CODE_TIMEOUT:
                return 'Timeout';
            case self::TRANSACTION_STATUS_CODE_DESFEITA:
                return 'Desfeita';
            case self::TRANSACTION_STATUS_CODE_INEXISTENTE:
                return 'Inexistente';
            case self::TRANSACTION_STATUS_CODE_NEGADO_ADMINISTRADORA:
                return 'Negado - Administradora';
            case self::TRANSACTION_STATUS_CODE_ESTORNADA:
                return 'Estornada';
            case self::TRANSACTION_STATUS_CODE_REPETIDA:
                return 'Repetida';
            case self::TRANSACTION_STATUS_CODE_ESTORNADA_CONCILIACAO:
                return 'Estornada Conciliação';
            case self::TRANSACTION_STATUS_CODE_CANCELADA_SEM_CONFIRMACAO:
                return 'Cancelada - Sem Confirmação';
            case self::TRANSACTION_STATUS_CODE_NEGADO_MGM:
                return 'Negado - MGM';
        }
        return '';
    }

    private function translateTransactionSign(string $sign)
    {

        switch ($sign) {

            case self::TRANSACTION_SIGN_MINUS:
                return 'Débito';
            case self::TRANSACTION_SIGN_MORE:
                return 'Crédito';
        }

        return '';
    }

    public function performStatement(stdClass $data)
    {

        //$transactions = $data->list_transactions ?? [];
        $transactions = array_reverse($data->list_transactions) ?? [];

        if (request('debug')) {

            echo '<pre>';
            print_r($transactions);
            echo '</pre>';
            exit;
        }

        /*$total = 0;
        $release_status_sim_Subseller_rate_closing_date_null = 0;
        foreach ($transactions as $item){


            if (isset($item->summary) && isset($item->details) && is_array($item->details) && count($item->details)) {

                $details = $item->details;
                $subSellerRateAmount = $details[0]->subseller_rate_amount ?? 0;

                $total += $subSellerRateAmount;

                if( $details[0]->release_status == 'S' && empty($details[0]->subseller_rate_confirm_date)){

                    $release_status_sim_Subseller_rate_closing_date_null++;
                }
            }
        }

        dd($release_status_sim_Subseller_rate_closing_date_null, $total, FoxUtils::formatMoney($total / 100));*/

        $totalInPeriod = 0;

        $data = [];

        foreach ($transactions as $item) {

            if (isset($item->summary) && isset($item->details) && is_array($item->details) && count($item->details)) {

                $summary = $item->summary;
                $details = $item->details;

                $orderId = $summary->order_id;
                $arrayOrderId = explode('-', $orderId);

                $transactionDate = $summary->transaction_date ?? '';                                                    // Data/Hora da transação.
                $paymentDate = $details[0]->payment_date ?? '';                                                         // Data calculada pelo sistema para o pagamento do valor ao subseller. Esta data pode ser alterada no momento da liberação do pagamento do item.
                $subSellerRateClosingDate = $details[0]->subseller_rate_closing_date ?? '';                             // Data em que o registro de pagamento foi enviado para a CIP.
                $subSellerRateConfirmDate = $details[0]->subseller_rate_confirm_date ?? '';                             // Data recebida no 2º arquivo de retorno da CIP com a confirmação pagamento a ser efetuado pelo banco.
                $subSellerRateAmount = $details[0]->subseller_rate_amount ?? 0;                                         // Valor do repasse para o subseller em centavos.
                $subSellerRateAmount = $summary->transaction_sign == '-' ? ($subSellerRateAmount * -1) : $subSellerRateAmount;

                foreach (['paymentDate', 'transactionDate', 'subSellerRateClosingDate', 'subSellerRateConfirmDate'] as $date) {


                    ${$date} = $this->formatDate(${$date});
                }

                $status = $this->getTransactionStatus($summary, $details[0]);

                // Definido em 24/04/2020
                // Se subseller_rate_confirm_date exibo subseller_rate_closing_date caso contrario, exibo o payment_date
                //$summaryDate = $subSellerRateConfirmDate ? $subSellerRateClosingDate : $paymentDate;
                // Definido em 25/04/2020
                $summaryDate = $subSellerRateConfirmDate ?? $subSellerRateClosingDate;

                $statement = (object)[
                    'orderId' => $arrayOrderId[0],
                    'subSellerRateAmount' => FoxUtils::formatMoney($subSellerRateAmount / 100),
                    'isInvite' => $subSellerRateAmount < 500,
                    'summaryStatus' => $status,
                    'summaryValue' => $subSellerRateAmount,
                    'summaryDate' => $summaryDate,
                    '_originalOrderId' => $orderId,
                    '_summaryTransactionStatusCode' => $summary->transaction_status_code,
                    '_summaryTranslateTransactionStatusCode' => $this->translateTransactionStatusCode($summary->transaction_status_code),
                    '_summaryTranslateSign' => $this->translateTransactionSign($summary->transaction_sign),
                    '_summarySign' => $summary->transaction_sign,
                    '_paymentDate' => $paymentDate,
                    '_transactionDate' => $transactionDate,
                    '_release_status' => $details[0]->release_status,
                    '_subSellerRateClosingDate' => $subSellerRateClosingDate,
                    '_subSellerRateConfirmDate' => $subSellerRateConfirmDate,
                ];

                switch (request('status')) {

                    case self::SEARCH_STATUS_ALL:

                        $data[] = $statement;
                        $totalInPeriod += $subSellerRateAmount;
                        break;

                    case self::SEARCH_STATUS_PAID:
                        if ($statement->summaryStatus['identify'] == self::SEARCH_STATUS_PAID) {

                            $data[] = $statement;
                            $totalInPeriod += $subSellerRateAmount;
                        }
                        break;
                    case self::SEARCH_STATUS_WAITING_LIQUIDATION:
                        if ($statement->summaryStatus['identify'] == self::SEARCH_STATUS_WAITING_LIQUIDATION) {

                            $data[] = $statement;
                            $totalInPeriod += $subSellerRateAmount;
                        }
                        break;
                    case self::SEARCH_STATUS_WAITING_FOR_VALID_POST:
                        if ($statement->summaryStatus['identify'] == self::SEARCH_STATUS_WAITING_FOR_VALID_POST) {

                            $data[] = $statement;
                            $totalInPeriod += $subSellerRateAmount;
                        }
                        break;
                    case self::SEARCH_STATUS_REVERSED:
                        if ($statement->summaryStatus['identify'] == self::SEARCH_STATUS_REVERSED) {

                            $data[] = $statement;
                            $totalInPeriod += $subSellerRateAmount;
                        }
                        break;
                }
            }
        }

        return [
            'totalInPeriod' => FoxUtils::formatMoney($totalInPeriod / 100),
            'transactions' => $data,
        ];
    }

    private function getTypeOfTransaction($summary, $details)
    {

        /*
         Estornada
            order_id = NOT NULL
            transaction_sign = -
            transaction_status_code = 0

        Aguardando postagem válida
            order_id = NOT NULL
            transaction_sign = +
            release_status = N
            transaction_status_code = ??????????????????????

        Aguardando liquidação
            order_id = NOT NULL
            transaction_sign = +
            release_status = S
            subseller_rate_confirm_date = NULL
            transaction_status_code = ??????????????????????

        Pago
            order_id = NOT NULL
            transaction_sign = +
            release_status = S
            subseller_rate_confirm_date = NOT NULL && <= today
            transaction_status_code = 0 ou 92
         * */

        $hasOrderId = empty($summary->order_id) ? false : true;
        $isTransactionSignCredit = $details->transaction_sign == '+';
        $isReleaseStatus = $details->release_status == 'S';
        $transactionStatusCode = $summary->transaction_status_code;
        $subSellerRateConfirmDate = $details->subseller_rate_confirm_date;

        if ($hasOrderId && !$isTransactionSignCredit && $transactionStatusCode == self::TRANSACTION_STATUS_CODE_APROVADO) {

            $type = self::SEARCH_STATUS_REVERSED;

        } elseif ($hasOrderId && $isTransactionSignCredit && !$isReleaseStatus) {

            $type = self::SEARCH_STATUS_WAITING_FOR_VALID_POST;

        } elseif ($hasOrderId && $isTransactionSignCredit && $isReleaseStatus && empty($subSellerRateConfirmDate)) {

            $type = self::SEARCH_STATUS_WAITING_LIQUIDATION;

        } elseif ($hasOrderId && $isTransactionSignCredit && $isReleaseStatus && !empty($subSellerRateConfirmDate) && in_array($transactionStatusCode, [self::TRANSACTION_STATUS_CODE_APROVADO, self::TRANSACTION_STATUS_CODE_ESTORNADA])) {

            $type = self::SEARCH_STATUS_PAID;

        } else {

            $type = self::SEARCH_STATUS_UNKNOW;
        }

        return $type;
    }

    private function getTransactionStatus($summary, $details)
    {

        $type = $this->getTypeOfTransaction($summary, $details);

        if ($type == self::SEARCH_STATUS_REVERSED) {

            $status = 'Estornada';
            $description = 'Solicitação do estorno: ' . $this->formatDate($summary->transaction_date);

        } elseif ($type == self::SEARCH_STATUS_WAITING_FOR_VALID_POST) {

            $status = 'Aguardando postagem válida';
            $description = 'Data da venda: ' . $this->formatDate($summary->transaction_date);

        } elseif ($type == self::SEARCH_STATUS_WAITING_LIQUIDATION) {

            $status = 'Aguardando liquidação';
            $description = 'Data da venda: ' . $this->formatDate($summary->transaction_date);

        } elseif ($type == self::SEARCH_STATUS_PAID) {

            $status = 'Pago';
            $description = 'Data da venda: ' . $this->formatDate($summary->transaction_date);

        } else {

            $status = '- - - -';
            $type = self::SEARCH_STATUS_UNKNOW;
            $description = '- - - ';
        }

        return [
            'status' => $status,
            'identify' => $type,
            'description' => $description,
        ];
    }

    private function formatDate($date)
    {


        if (!empty($date)) {

            try {

                $date = Carbon::parse($date)->format('d/m/Y');
            } catch (Exception $exception) {

            }
        }

        return $date;
    }
}
