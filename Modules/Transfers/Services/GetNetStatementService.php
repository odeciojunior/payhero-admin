<?php


namespace Modules\Transfers\Services;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\FoxUtils;
use stdClass;

class GetNetStatementService
{

    protected $data = [];

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
        $transactions = array_reverse($data->list_transactions) ?? [];
        //dd($transactions);
        /*echo '<pre>';
        print_r($transactions);
        exit;*/
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
        array_map(function ($item) {

            if (isset($item->summary) && isset($item->details) && is_array($item->details) && count($item->details)) {

                $summary = $item->summary;
                $details = $item->details;

                $orderId = $summary->order_id;
                $arrayOrderId = explode('-', $orderId);

                $transactionDate = $summary->transaction_date ?? '';                                                    // Data/Hora da transação.

                //$installmentDate = $details[0]->installment_date ?? '';
                //$installmentAmount = $details[0]->installment_amount ?? 0;
                $paymentDate = $details[0]->payment_date ?? '';                                                         // Data calculada pelo sistema para o pagamento do valor ao subseller. Esta data pode ser alterada no momento da liberação do pagamento do item.
                $subSellerRateClosingDate = $details[0]->subseller_rate_closing_date ?? '';                             // Data em que o registro de pagamento foi enviado para a CIP.
                $subSellerRateConfirmDate = $details[0]->subseller_rate_confirm_date ?? '';                             // Data recebida no 2º arquivo de retorno da CIP com a confirmação pagamento a ser efetuado pelo banco.
                $subSellerRateAmount = $details[0]->subseller_rate_amount ?? 0;                                         // Valor do repasse para o subseller em centavos.
                //$subSellerRatePercentage = $details[0]->subseller_rate_percentage ?? 0;

                /*try {
                    if (request('statement_data_type') == 'liquidation_date') {
                        $transactionDate = Carbon::parse($transactionDate)->format('d/m/Y');
                    } else {
                        $transactionDate = Carbon::parse($transactionDate)->format('d/m/Y');
                    }
                } catch (Exception $exception) {
                }*/

                foreach (['installmentDate', 'paymentDate', 'transactionDate'] as $date) {

                    if (!empty(${$date})) {

                        try {

                            ${$date} = Carbon::parse(${$date})->format('d/m/Y');
                        } catch (Exception $exception) {

                        }
                    }
                }

                $status = $this->getStatus(
                    $details[0]->release_status,                                                                        // Indicada se o agendamento já foi liberado para pagamento S/N.
                    $details[0]->subseller_rate_confirm_date,
                    $paymentDate
                );

                $statement = (object)[
                    'orderId' => $arrayOrderId[0],
                    'originalOrderId' => $orderId,
                    'transactionDate' => $transactionDate,
                    //'installmentDate' => $installmentDate,
                    'paymentDate' => $paymentDate,
                    //'installmentAmount' => FoxUtils::formatMoney($installmentAmount / 100),
                    'subSellerRateAmount' => FoxUtils::formatMoney($subSellerRateAmount / 100),
                    //'subSellerRateSumTotalAmount' => $subSellerRateAmount,
                    //'subSellerRatePercentage' => $subSellerRatePercentage,
                    //'subSellerRateClosingDate' => $subSellerRateClosingDate ? Carbon::parse($subSellerRateClosingDate)->format('d/m/Y') : '',
                    //'subSellerRateConfirmDate' => $subSellerRateConfirmDate ? Carbon::parse($subSellerRateConfirmDate)->format('d/m/Y') : '',
                    'statusNumeric' => $status[0],
                    'status' => $status[1],
                    'isInvite' => $subSellerRateAmount < 500,
                    'summaryStatus' => $this->translateTransactionStatusCode($summary->transaction_status_code),
                    'summaryType' => $this->translateTransactionSign($summary->transaction_sign),
                    'summaryValue' => $summary->transaction_sign == '-' ? ($subSellerRateAmount * -1) : $subSellerRateAmount,
                ];

                if (request('status') == 'all' || !in_array(request('status'), ['all', 1, 2, 3])) {
                    $this->data[] = $statement;
                } elseif (request('status') == $status) {
                    $this->data[] = $statement;
                }
            }
        }, $transactions);

        return $this->data;
    }

    private function getStatus($releaseStatus, $rateConfirmDate)
    {
        $transferPresent = (new Transfer())->present();

        if ($releaseStatus == 'N') {

            return [1, $transferPresent->getStatusGetnet(1)];
        } elseif ($releaseStatus == 'S' && is_null($rateConfirmDate)) {

            return [2, $transferPresent->getStatusGetnet(2)];
        } else {

            return [3, $transferPresent->getStatusGetnet(3)];
        }
    }
}
