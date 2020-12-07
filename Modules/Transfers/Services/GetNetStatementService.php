<?php


namespace Modules\Transfers\Services;

use Carbon\Carbon;
use Exception;
use Modules\Transfers\Getnet\Details;
use Modules\Transfers\Getnet\Order;
use Modules\Transfers\Getnet\StatementItem;
use stdClass;
use Vinkla\Hashids\Facades\Hashids;

class GetNetStatementService
{

    const SUMMARY_TRANSACTION_TYPE_CREDITO_A_VISTA = 1;
    const SUMMARY_TRANSACTION_TYPE_CREDITO_PARCELADO_LOJISTA = 2;
    const SUMMARY_TRANSACTION_TYPE_CREDITO_PARCELADO_ADMINBISTRADORA = 3;
    const SUMMARY_TRANSACTION_TYPE_DEBITO = 4;
    const SUMMARY_TRANSACTION_TYPE_CANCELAMENTO = 5;
    const SUMMARY_TRANSACTION_TYPE_CHARGEBACK = 6;
    const SUMMARY_TRANSACTION_TYPE_BOLETO = 7;

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

    protected array $statementItems = [];

    protected float $totalInPeriod;
    protected float $totalAdjustment = 0;
    protected float $totalTransactions = 0;
    protected float $totalChargeback = 0;
    protected float $totalReversed = 0;

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

    public function performStatement(stdClass $data)
    {

        $transactions = array_reverse($data->list_transactions) ?? [];
        $adjustments = array_reverse($data->adjustments) ?? [];
        $chargeback = array_reverse($data->chargeback) ?? [];

        if (request('debug')) {

            echo '<pre>';
            print_r($data);
            echo '</pre>';
            exit;
        }

        $this->totalInPeriod = 0;

        $this->preparesNodeTransactions($transactions);
        $this->preparesNodeAdjustments($adjustments);

        return [
            'items' => $this->statementItems,
            'totalInPeriod' => $this->totalInPeriod,
            'totalAdjustment' => $this->totalAdjustment,
            'totalChargeback' => $this->totalChargeback,
            'totalReversed' => $this->totalReversed,
            'totalTransactions' => $this->totalTransactions,
        ];
    }

    private function setOrderFromGetNetOrderId($getOrderId): ?Order
    {

        if (!empty($getOrderId)) {

            $arrayOrderId = explode('-', $getOrderId);

            if (count($arrayOrderId)) {

                $hashId = $arrayOrderId[0];

                $order = new Order();
                return $order->setSaleId(current(Hashids::connection('sale_id')->decode($arrayOrderId[0])))
                    ->setHashId($hashId)
                    ->setOrderId($getOrderId);
            } else {

                //TODO
                return null;
            }
        }
    }

    private function preparesNodeTransactions($transactions): void
    {

        foreach ($transactions as $item) {

            if (count($item->details) == 0) {

                dd('TODO::A', $item);
            } else if (count($item->details) > 1) {

                dd('TODO::B', $item);
            }

            if (isset($item->summary) && isset($item->details) && is_array($item->details) && count($item->details) == 1) {

                $summary = $item->summary;
                $details = $item->details[0];

                $transactionType = $summary->transaction_type;
                /*$summary_transaction_sign = $summary->transaction_sign;
                $details_transaction_sign = $details->transaction_sign;*/

                $amount = $details->subseller_rate_amount / 100;
                $amount = $details->transaction_sign == '-' ? ($amount * -1) : $amount;

                $paymentDate = $details->payment_date ?? '';
                $transactionDate = $details->transaction_date ?? '';
                $subSellerRateClosingDate = $details->subseller_rate_closing_date ?? '';
                $subSellerRateConfirmDate = $details->subseller_rate_confirm_date ?? '';

                foreach (['paymentDate', 'transactionDate', 'subSellerRateClosingDate', 'subSellerRateConfirmDate'] as $date) {

                    if ($date) {

                        ${$date} = $this->formatDate(${$date});
                    }
                }

                if ($transactionType == self::SUMMARY_TRANSACTION_TYPE_CANCELAMENTO) {

                    $paidWith = null;
                    $type = StatementItem::TYPE_REVERSED;
                    $this->totalReversed += $amount;

                } else if ($transactionType == self::SUMMARY_TRANSACTION_TYPE_CHARGEBACK) {

                    $paidWith = null;
                    $type = StatementItem::TYPE_CHARGEBACK;
                    $this->totalChargeback += $amount;

                } else {

                    if ($summary->product_id == 101) {

                        $paidWith = 'Boleto';
                    } elseif (in_array($summary->product_id, [1, 2, 3, 4, 5, 11, 12, 13])) {

                        $paidWith = 'Cartão';
                    } else {
                        dd('TODO::C', $item);
                    }

                    $type = StatementItem::TYPE_TRANSACTION;
                    $this->totalTransactions += $amount;
                }

                /*
                 Estornado
                    order_id = NOT NULL
                    transaction_sign = -
                    transaction_status_code = 0

                Aguardando postagem válida
                    order_id = NOT NULL
                    transaction_sign = +
                    release_status = N

                Aguardando liquidação
                    order_id = NOT NULL
                    transaction_sign = +
                    release_status = S
                    subseller_rate_confirm_date = NULL

                Aguardando saque
                    order_id = NOT NULL
                    transaction_sign = +
                    release_status = N
                    subseller_rate_confirm_date = NULL

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

                $details = new Details();

                if ($hasOrderId && !$isTransactionSignCredit && $transactionStatusCode == self::TRANSACTION_STATUS_CODE_APROVADO) {

                    $details->setStatus('Estornado')
                        ->setDescription('Solicitação do estorno: ' . $this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_REVERSED);

                } elseif ($hasOrderId && $isTransactionSignCredit && !$isReleaseStatus) {

                    $details->setStatus('Aguardando postagem válida')
                        ->setDescription('Data da venda: ' . $this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_WAITING_FOR_VALID_POST);

                } elseif ($hasOrderId && $isTransactionSignCredit && !$isReleaseStatus && empty($subSellerRateConfirmDate)) {

                    $details->setStatus('Aguardando saque')
                        ->setDescription('Data da venda: ' . $this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_WAITING_WITHDRAWAL);

                } elseif ($hasOrderId && $isTransactionSignCredit && $isReleaseStatus && empty($subSellerRateConfirmDate)) {

                    $details->setStatus('Aguardando liquidação')
                        ->setDescription('Data da venda: ' . $this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_WAITING_LIQUIDATION);

                } elseif ($hasOrderId && $isTransactionSignCredit && $isReleaseStatus && !empty($subSellerRateConfirmDate) && in_array($transactionStatusCode, [self::TRANSACTION_STATUS_CODE_APROVADO, self::TRANSACTION_STATUS_CODE_ESTORNADA])) {

                    $details->setStatus('Liquidado')
                        ->setDescription('Data da venda: ' . $this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_PAID);

                } else {

                    dd('TODO::D', $item);
                }

                $statementItem = new StatementItem();

                $statementItem->order = $this->setOrderFromGetNetOrderId($summary->order_id);
                $statementItem->details = $details;
                $statementItem->amount = $amount;
                $statementItem->paidWith = $paidWith;
                $statementItem->type = $type;
                $statementItem->transactionDate = $transactionDate;
                $statementItem->expectedDate = $subSellerRateConfirmDate ? $subSellerRateClosingDate : $paymentDate;
                $statementItem->realizedDate = '';

                $this->totalInPeriod += $amount;
                $this->statementItems[] = $statementItem;
            }
        }
    }

    private function preparesNodeAdjustments($adjustments): void
    {

        foreach ($adjustments as $adjustment) {

            if ($adjustment->cnpj_marketplace != $adjustment->cpfcnpj_subseller) {

                $amount = $adjustment->adjustment_amount / 100;
                $amount = $adjustment->transaction_sign == '-' ? ($amount * -1) : $amount;

                $paymentDate = $adjustment->payment_date ?? '';
                $adjustmentDate = $adjustment->adjustment_date ?? '';
                $subSellerRateClosingDate = $adjustment->subseller_rate_closing_date ?? '';
                $subSellerRateConfirmDate = $adjustment->subseller_rate_confirm_date ?? '';

                foreach (['paymentDate', 'adjustmentDate', 'subSellerRateClosingDate', 'subSellerRateConfirmDate'] as $date) {

                    if ($date) {

                        ${$date} = $this->formatDate(${$date});
                    }
                }

                $details = new Details();
                $details->setStatus('Ajuste de ' . ($adjustment->transaction_sign == '+' ? 'crédito' : 'débito'))
                    ->setDescription($adjustment->adjustment_reason)
                    ->setType($adjustment->transaction_sign == '+' ? Details::STATUS_ADJUSTMENT_CREDIT : Details::STATUS_ADJUSTMENT_DEBIT);

                $statementItem = new StatementItem();

                $statementItem->amount = $amount;
                $statementItem->details = $details;
                $statementItem->type = StatementItem::TYPE_ADJUSTMENT;
                $statementItem->transactionDate = $adjustmentDate;
                $statementItem->expectedDate = $subSellerRateConfirmDate ? $subSellerRateClosingDate : $paymentDate;
                $statementItem->realizedDate = '';

                $this->totalInPeriod += $amount;
                $this->totalAdjustment += $amount;
                $this->statementItems[] = $statementItem;
            }
        }
    }

    private function preparesNodeChargeback(): void
    {

    }

    private function formatDate(string $date): string
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
