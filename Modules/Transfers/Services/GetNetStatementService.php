<?php

namespace Modules\Transfers\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Redis;
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
    protected array $filters = [];

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

    private function canAddStatementItem($date, $status, $paymentMethod): bool
    {

        $isValidStatusFilter = true;
        $isValidPaymentMethodFilter = true;

        if (array_key_exists('start_date', $this->filters) && array_key_exists('end_date', $this->filters)) {

            $startDate = $this->filters['start_date']->format('Ymd');
            $endDate = $this->filters['end_date']->format('Ymd');
            $date = Carbon::createFromFormat('d/m/Y', $date)->format('Ymd');

            if ($date < $startDate || $date > $endDate) {

                return false;
            }
        }

        if ($status && array_key_exists('status', $this->filters) && $this->filters['status'] != 'ALL') {

            if ($status !== $this->filters['status']) {

                $isValidStatusFilter = false;
            }
        }

        if (array_key_exists('payment_method', $this->filters) && $this->filters['payment_method'] != 'ALL') {

            if (!$paymentMethod || ($paymentMethod !== $this->filters['payment_method'])) {

                $isValidPaymentMethodFilter = false;
            }
        }

        return $isValidStatusFilter && $isValidPaymentMethodFilter;
    }

    public function performWebStatement(stdClass $data, array $filters = [])
    {

        $this->filters = $filters;

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
            'items' => collect($this->statementItems)->sortByDesc('sequence')->values()->all(),
            'totalInPeriod' => $this->totalInPeriod,
            'totalAdjustment' => $this->totalAdjustment,
            'totalChargeback' => $this->totalChargeback,
            'totalReversed' => $this->totalReversed,
            'totalTransactions' => $this->totalTransactions,
        ];
    }

    public function performStatement(stdClass $data, array $filters = [])
    {

        if (isset($data->errors)) {

            //dd($data->errors);
            $exception = new Exception('Houve um erro ao processar a requisição na getnet em ' . __METHOD__ . ' :: ' . $data->errors[0]->message);
            report($exception);
        }

        $this->filters = $filters;

        $transactions = array_reverse($data->list_transactions) ?? [];
        $adjustments = array_reverse($data->adjustments) ?? [];
        $chargeback = array_reverse($data->chargeback) ?? [];

        $this->totalInPeriod = 0;

        $this->preparesNodeTransactions($transactions);
        $this->preparesNodeAdjustments($adjustments);

        return [
            'items' => collect($this->statementItems)->sortByDesc('sequence')->values()->all(),
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

                report(new Exception('GETNET::STATEMENT $item->details == 0'));
            } else if (count($item->details) > 1) {

                report(new Exception('GETNET::STATEMENT $item->details > 1'));
            }

            if (isset($item->summary) && isset($item->details) && is_array($item->details) && count($item->details) == 1) {

                $summary = $item->summary;
                $details = $item->details[0];

                $transactionType = $summary->transaction_type;

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

                } else if ($transactionType == self::SUMMARY_TRANSACTION_TYPE_CHARGEBACK) {

                    $paidWith = null;
                    $type = StatementItem::TYPE_CHARGEBACK;

                } else {

                    if ($summary->product_id == 101) {

                        $paidWith = StatementItem::PAID_WITH_BANK_SLIP;
                    } elseif (in_array($summary->product_id, [1, 2, 3, 4, 5, 11, 12, 13])) {

                        $paidWith = StatementItem::PAID_WITH_CREDIT_CARD;
                    } else {

                        $paidWith = null;
                        report(new Exception('GETNET::STATEMENT UNKNOW ERROR'));
                    }

                    $type = StatementItem::TYPE_TRANSACTION;
                }

                $orderFromGetNetOrderId = $this->setOrderFromGetNetOrderId($summary->order_id);
                $hasOrderId = empty($summary->order_id) ? false : true;
                $isTransactionCredit = $details->transaction_sign == '+';
                $isReleaseStatus = $details->release_status == 'S';
                $hasValidTracking = (boolean)Redis::connection('redis-statement')->get("sale:has:tracking:{$orderFromGetNetOrderId->getSaleId()}");
                $transactionStatusCode = $summary->transaction_status_code;

                $details = new Details();

                if ($hasOrderId && !$isTransactionCredit && $transactionStatusCode == self::TRANSACTION_STATUS_CODE_APROVADO) {

                    $details->setStatus('Estornado')
                        ->setDescription('Solicitação do estorno: ' . $this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_REVERSED);

                } elseif ($hasOrderId && $isTransactionCredit && !$isReleaseStatus && !$hasValidTracking) {

                    $details->setStatus('Aguardando postagem válida')
                        ->setDescription('Data da venda: ' . $this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_WAITING_FOR_VALID_POST);

                } elseif ($hasOrderId && $isTransactionCredit && $hasValidTracking && !$isReleaseStatus) {

                    $details->setStatus('Aguardando saque')
                        ->setDescription('Data da venda: ' . $this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_WAITING_WITHDRAWAL);

                } elseif ($hasOrderId && $isTransactionCredit && $hasValidTracking && $isReleaseStatus && empty($subSellerRateConfirmDate)) {

                    $details->setStatus('Aguardando liquidação')
                        ->setDescription('Data da venda: ' . $this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_WAITING_LIQUIDATION);

                } elseif (
                    (
                        $hasOrderId && $isTransactionCredit && $hasValidTracking && !empty($subSellerRateConfirmDate)
                        && in_array($transactionStatusCode, [self::TRANSACTION_STATUS_CODE_APROVADO, self::TRANSACTION_STATUS_CODE_ESTORNADA])
                    )
                    ||
                    ($transactionStatusCode == self::TRANSACTION_STATUS_CODE_ESTORNADA)
                ) {

                    $details->setStatus('Liquidado')
                        ->setDescription('Data da venda: ' . $this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_PAID);

                } else {

                    /*$details->setStatus(json_encode([
                        'hasOrderId' => $hasOrderId,
                        'isTransactionCredit' => $isTransactionCredit,
                        'isReleaseStatus' => $isReleaseStatus,
                        'hasValidTracking' => $hasValidTracking,
                        'transactionStatusCode' => $transactionStatusCode,
                        'subSellerRateConfirmDate' => $subSellerRateConfirmDate,
                    ]))*/
                    $details->setStatus('-')
                        ->setDescription('Data da venda: ' . $this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_ERROR);
                }

                $date = $subSellerRateConfirmDate ? $subSellerRateClosingDate : $paymentDate;

                if ($this->canAddStatementItem($date, $details->getType(), $paidWith)) {

                    $statementItem = new StatementItem();

                    $statementItem->order = $orderFromGetNetOrderId;
                    $statementItem->details = $details;
                    $statementItem->amount = $amount;
                    $statementItem->isInvite = $amount <= 5.00;
                    $statementItem->paidWith = $paidWith;
                    $statementItem->type = $type;
                    $statementItem->transactionDate = $transactionDate;
                    $statementItem->date = $date;
                    $statementItem->subSellerRateConfirmDate = $subSellerRateConfirmDate;
                    $statementItem->sequence = $statementItem->date ? (Carbon::createFromFormat('d/m/Y', $statementItem->date)->format('Ymd')) : 0;

                    $this->totalInPeriod += $amount;
                    $this->statementItems[] = $statementItem;

                    if ($transactionType == self::SUMMARY_TRANSACTION_TYPE_CANCELAMENTO) {

                        $this->totalReversed += $amount;

                    } else if ($transactionType == self::SUMMARY_TRANSACTION_TYPE_CHARGEBACK) {

                        $this->totalChargeback += $amount;

                    } else {

                        $this->totalTransactions += $amount;
                    }
                }
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

                $date = $subSellerRateConfirmDate ? $subSellerRateClosingDate : $paymentDate;

                if ($this->canAddStatementItem($date, $details->getType(), null)) {

                    $statementItem = new StatementItem();

                    $statementItem->amount = $amount;
                    $statementItem->details = $details;
                    $statementItem->type = StatementItem::TYPE_ADJUSTMENT;
                    $statementItem->transactionDate = $adjustmentDate;
                    $statementItem->date = $date;
                    $statementItem->subSellerRateConfirmDate = $subSellerRateConfirmDate;
                    $statementItem->sequence = $statementItem->date ? (Carbon::createFromFormat('d/m/Y', $statementItem->date)->format('Ymd')) : 0;

                    $this->totalInPeriod += $amount;
                    $this->totalAdjustment += $amount;
                    $this->statementItems[] = $statementItem;
                }

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
