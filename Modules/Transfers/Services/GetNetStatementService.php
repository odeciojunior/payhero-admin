<?php

namespace Modules\Transfers\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\PendingDebt;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Transfers\Getnet\Details;
use Modules\Transfers\Getnet\Order;
use Modules\Transfers\Getnet\StatementItem;
use stdClass;
use Vinkla\Hashids\Facades\Hashids;

class GetNetStatementService
{

    const SUMMARY_TRANSACTION_TYPE_CREDITO_A_VISTA = 1;
    const SUMMARY_TRANSACTION_TYPE_CREDITO_PARCELADO_LOJISTA = 2;
    const SUMMARY_TRANSACTION_TYPE_CREDITO_PARCELADO_ADMINISTRADORA = 3;
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

    public function performWebStatement(stdClass $data, array $filters = [], $limit = false)
    {

        $this->filters = $filters;

        $transactions = array_reverse($data->list_transactions ?? []);
        $adjustments = array_reverse($data->adjustments ?? []);
        //$chargeback = array_reverse($data->chargeback ?? []);

        if (request('debug')) {

            echo '<pre>';
            print_r($data);
            echo '</pre>';
            exit;
        }

        $this->totalInPeriod = 0;

        if ($this->filters['status'] != 'PENDING_DEBIT') {

            $this->preparesNodeTransactions($transactions);
            $this->preparesNodeAdjustments($adjustments);
        }

        if (in_array($this->filters['payment_method'], ['ALL', 'PIX']) && $this->filters['status'] != 'PENDING_DEBIT') {
            $this->preparesDatabasePixWithSaleSearch();
        }

        $this->preparesDatabasePendingDebtsWithSaleSearch();
        $items = collect($this->statementItems)->sortByDesc('sequence')->values();

        return [
            'items' => !$limit ? $items->all() : $items->take($limit),
            'totalInPeriod' => number_format($this->totalInPeriod, 2),
            'totalAdjustment' => $this->totalAdjustment,
            'totalChargeback' => $this->totalChargeback,
            'totalReversed' => $this->totalReversed,
            'totalTransactions' => $this->totalTransactions,
        ];
    }

    private function preparesNodeTransactions($transactions): void
    {

        foreach ($transactions as $item) {

            if (count($item->details) == 0) {

                report(new Exception('GETNET::STATEMENT $item->details == 0'));
            } elseif (count($item->details) > 1) {

                report(new Exception('GETNET::STATEMENT $item->details > 1'));
            }

            if (isset($item->summary) && isset($item->details) && is_array($item->details) && count($item->details) == 1) {

                $summary = $item->summary;
                $details = $item->details[0];

                $transactionType = $summary->transaction_type;

                $amount = $details->subseller_rate_amount / 100;
                $amount = $details->transaction_sign == '-' ? ($amount * -1) : $amount;

                $paymentDateNumeric = $details->payment_date ? Carbon::parse($details->payment_date)->format('Ymd') : null;
                $paymentDate = $details->payment_date ?? '';
                $transactionDate = $details->transaction_date ?? '';
                $subSellerRateClosingDate = $details->subseller_rate_closing_date ?? '';
                $subSellerRateConfirmDate = $details->subseller_rate_confirm_date ?? '';

                foreach ([
                             'paymentDate',
                             'transactionDate',
                             'subSellerRateClosingDate',
                             'subSellerRateConfirmDate'
                         ] as $date) {

                    if ($date) {

                        ${$date} = $this->formatDate(${$date});
                    }
                }

                if ($transactionType == self::SUMMARY_TRANSACTION_TYPE_CANCELAMENTO) {

                    $paidWith = null;
                    $type = StatementItem::TYPE_REVERSED;

                } elseif ($transactionType == self::SUMMARY_TRANSACTION_TYPE_CHARGEBACK) {

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

                if ($hasOrderId && $this->isDigitalProduct($summary->order_id)) {

                    $hasValidTracking = true;
                } else {

                    $hasValidTracking = (boolean)Redis::connection('redis-statement')->get("sale:has:tracking:{$orderFromGetNetOrderId->getSaleId()}") ?? true;
                }
                $transactionStatusCode = $summary->transaction_status_code;

                $details = new Details();

                if ($hasOrderId && !$isTransactionCredit && $transactionStatusCode == self::TRANSACTION_STATUS_CODE_APROVADO) {

                    $details->setStatus('Estornado')
                        ->setDescription($this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_REVERSED);

                } elseif ($hasOrderId && $isTransactionCredit && !$isReleaseStatus && !$hasValidTracking) {

                    $details->setStatus('Aguardando postagem válida')
                        ->setDescription($this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_WAITING_FOR_VALID_POST);

                } elseif ($hasOrderId && $isTransactionCredit && $hasValidTracking && !$isReleaseStatus && $paymentDateNumeric && ($paymentDateNumeric > date('Ymd'))) {

                    $details->setStatus('Aguardando Liberação')
                        ->setDescription($this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_WAITING_RELEASE);

                } elseif ($hasOrderId && $isTransactionCredit && $hasValidTracking && !$isReleaseStatus) {

                    $details->setStatus('Aguardando saque')
                        ->setDescription($this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_WAITING_WITHDRAWAL);

                } elseif (
                    (
                        $hasOrderId && $isTransactionCredit && $hasValidTracking && $isReleaseStatus && empty($subSellerRateConfirmDate)
                    )
                    ||
                    (
                        $transactionStatusCode == self::TRANSACTION_STATUS_CODE_ESTORNADA && empty($subSellerRateConfirmDate)
                    )
                ) {

                    $details->setStatus('Aguardando liquidação')
                        ->setDescription($this->formatDate($summary->transaction_date))
                        ->setType(Details::STATUS_WAITING_LIQUIDATION);

                } elseif (
                    (
                        $hasOrderId && $isTransactionCredit && $hasValidTracking && !empty($subSellerRateConfirmDate)
                        && in_array($transactionStatusCode,
                            [self::TRANSACTION_STATUS_CODE_APROVADO, self::TRANSACTION_STATUS_CODE_ESTORNADA])
                    )
                    ||
                    (
                        $transactionStatusCode == self::TRANSACTION_STATUS_CODE_ESTORNADA && !empty($subSellerRateConfirmDate)
                    )
                ) {

                    $details->setStatus('Liquidado')
                        ->setDescription($this->formatDate($summary->transaction_date))
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
                        ->setDescription($this->formatDate($summary->transaction_date))
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
                    $statementItem->sequence = $statementItem->date ? (Carbon::createFromFormat('d/m/Y',
                        $statementItem->date)->format('Ymd')) : 0;

                    $this->totalInPeriod += $amount;
                    $this->statementItems[] = $statementItem;

                    if ($transactionType == self::SUMMARY_TRANSACTION_TYPE_CANCELAMENTO) {

                        $this->totalReversed += $amount;

                    } elseif ($transactionType == self::SUMMARY_TRANSACTION_TYPE_CHARGEBACK) {

                        $this->totalChargeback += $amount;

                    } else {

                        $this->totalTransactions += $amount;
                    }
                }
            }
        }
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

    public function setOrderFromGetNetOrderId($getOrderId): ?Order
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

    private function isDigitalProduct($orderId): bool
    {

        $findTextDigital = '-D';
        $isDigital = strpos($orderId, $findTextDigital);

        return $isDigital !== false;
    }

    private function canAddStatementItem($date, $status, $paymentMethod): bool
    {

        $isValidStatusFilter = true;
        $isValidPaymentMethodFilter = true;

        if (!array_key_exists('sale', $this->filters)) {

            if (array_key_exists('start_date', $this->filters) && array_key_exists('end_date', $this->filters)) {

                $startDate = $this->filters['start_date']->format('Ymd');
                $endDate = $this->filters['end_date']->format('Ymd');
                $date = Carbon::createFromFormat('d/m/Y', $date)->format('Ymd');

                if ($date < $startDate || $date > $endDate) {

                    return false;
                }
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

                foreach ([
                             'paymentDate',
                             'adjustmentDate',
                             'subSellerRateClosingDate',
                             'subSellerRateConfirmDate'
                         ] as $date) {

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

                    $order = null;

                    $findTextChargeBack = 'Chargeback da venda #';
                    $findTextReverse = 'Estorno da venda:';
                    $adjustmentReason = $adjustment->adjustment_reason;

                    $adjustIsChargeBack = strpos($adjustmentReason, $findTextChargeBack);
                    $adjustIsReverse = strpos($adjustmentReason, $findTextReverse);

                    if ($adjustIsChargeBack !== false || $adjustIsReverse !== false) {

                        $hashId = explode('#', $adjustmentReason);

                        if (count($hashId) == 2) {

                            // TODO Rupert em 02/03/2021: Para evitar consulta vou montar um objeto com a propriedade "setOrderId" incompleta

                            $order = new Order();
                            $order->setSaleId(current(Hashids::connection('sale_id')->decode($hashId[1])))
                                ->setHashId($hashId[1])
                                ->setOrderId('-');
                            /*$saleId = current(Hashids::connection('sale_id')->decode($hashId[1]));

                            if (!empty($saleId)) {

                                $sale = Sale::select('gateway_order_id')->find($saleId);
                                $gateway_order_id = $sale->gateway_order_id;
                                $orderFromGetNetOrderId = $this->setOrderFromGetNetOrderId($gateway_order_id);
                            }*/
                        }
                    }

                    $statementItem = new StatementItem();

                    $statementItem->amount = $amount;
                    $statementItem->order = $order;
                    $statementItem->details = $details;
                    $statementItem->type = StatementItem::TYPE_ADJUSTMENT;
                    $statementItem->transactionDate = $adjustmentDate;
                    $statementItem->date = '';//$date; // TODO Rupert em 02/03/2021: A pedido do Julio
                    $statementItem->subSellerRateConfirmDate = $subSellerRateConfirmDate;
                    $statementItem->sequence = $statementItem->date ? (Carbon::createFromFormat('d/m/Y',
                        $statementItem->date)->format('Ymd')) : 0;

                    $this->totalInPeriod += $amount;
                    $this->totalAdjustment += $amount;
                    $this->statementItems[] = $statementItem;
                }

            }
        }
    }

    private function preparesDatabasePixWithSaleSearch(): void
    {
        $companyId = $this->filters['company_id'];

        $pixSales = Sale::select('transactions.value as transaction_value', 'transactions.status as transaction_status',
            'transactions.release_date', 'transactions.withdrawal_id',
            'sales.start_date', 'sales.end_date', 'sales.has_valid_tracking', 'sales.id', 'sales.delivery_id', 'transactions.gateway_released_at')
            ->join('transactions', 'transactions.sale_id', '=', 'sales.id')
            ->where('payment_method', Sale::PIX_PAYMENT)
            ->where('transactions.type', Transaction::TYPE_PRODUCER)
            ->where('sales.status', Sale::STATUS_APPROVED)
            ->whereNotNull('transactions.withdrawal_id')
            ->whereCompanyId($companyId);

        if (array_key_exists('sale_id', $this->filters) && !empty($this->filters['sale_id'])) {
            $pixSales->where('sales.id', $this->filters['sale_id']);
        }

        if(request('dateRange')) {

            $dates = explode(' - ', request('dateRange') ?? '');
            $startDate = Carbon::createFromFormat('d/m/Y', $dates[0]);
            $endDate = Carbon::createFromFormat('d/m/Y', $dates[1]);
            $pixSales->whereDate('sales.start_date', '>=', $startDate->format('Y-m-d'));
            $pixSales->whereDate('sales.end_date', '<=', $endDate->format('Y-m-d'));
        }

        $pixSales = $pixSales->get();

        foreach ($pixSales as $pix_sale) {
            $details = new Details();

            if (!empty($pix_sale->delivery_id)) {

                if ($pix_sale->has_valid_tracking == false) {
                    $details->setStatus('Aguardando postagem válida')
                        ->setDescription($pix_sale->start_date ? $this->formatDate($pix_sale->start_date) : '')
                        ->setType(Details::STATUS_WAITING_FOR_VALID_POST);
                } elseif ($pix_sale->transaction_status == 'transfered') {
                    $details->setStatus('Liquidado')
                        ->setDescription($pix_sale->start_date ? $this->formatDate($pix_sale->start_date) : '')
                        ->setType(Details::STATUS_PAID);
                } else {
                    $details->setStatus('Aguardando saque')
                        ->setDescription($pix_sale->start_date ? $this->formatDate($pix_sale->start_date) : '')
                        ->setType(Details::STATUS_WAITING_WITHDRAWAL);
                }

            } elseif (empty($pix_sale->release_date) || $pix_sale->release_date <= Carbon::now()->format('Y-m-d')) {
                $details->setStatus('Aguardando liquidação')
                    ->setDescription($pix_sale->start_date ? $this->formatDate($pix_sale->start_date) : '')
                    ->setType(Details::STATUS_WAITING_LIQUIDATION);
            } elseif (empty($pix_sale->withdrawal_id)) {
                $details->setStatus('Aguardando saque')
                    ->setDescription($pix_sale->start_date ? $this->formatDate($pix_sale->start_date) : '')
                    ->setType(Details::STATUS_WAITING_WITHDRAWAL);
            } else {
                $details->setStatus('Liquidado')
                    ->setDescription($pix_sale->start_date ? $this->formatDate($pix_sale->start_date) : '')
                    ->setType(Details::STATUS_PAID);
            }

            $sequence = $pix_sale->end_date ? Carbon::parse($pix_sale->end_date)->format('Ymd') : 0;
            $value = $pix_sale->transaction_value / 100;

            $order = new Order();
            $order = $order->setSaleId($pix_sale->id)
                ->setHashId(Hashids::connection('sale_id')->encode($pix_sale->id))
                ->setOrderId('');

            $statementItem = new StatementItem();
            $statementItem->amount = $value;
            $statementItem->order = $order;
            $statementItem->details = $details;
            $statementItem->type = StatementItem::TYPE_TRANSACTION;
            $statementItem->transactionDate = $pix_sale->end_date ? Carbon::parse($pix_sale->end_date)->format('d/m/Y') : '';
            $statementItem->date = $pix_sale->end_date ? Carbon::parse($pix_sale->end_date)->format('d/m/Y') : '';
            $statementItem->subSellerRateConfirmDate = $pix_sale->gateway_released_at ?? '';
            $statementItem->sequence = $sequence;

            $this->totalInPeriod += $value;
            $this->totalChargeback += $value;
            $this->statementItems[] = $statementItem;
        }
    }

    private function preparesDatabasePendingDebtsWithSaleSearch(): void
    {

        $pendingDebts = [];
        $companyId = $this->filters['company_id'];

        if (array_key_exists('sale_id', $this->filters) && !empty($this->filters['sale_id'])) {

            // Em 18/03/2021: Se carregarmos os REVERSED mostraremos um valor negativo.
            // Ex: kZ7k7VNZ logado na conta de VITOR MONTEIRO DA SILVA
            $saleId = $this->filters['sale_id'];
            $pendingDebts = PendingDebt::whereSaleId($saleId)
                ->whereIn('type', ['ADJUSTMENT'])
                ->whereCompanyId($companyId)
                ->get();

        } elseif (array_key_exists('withdrawal_id', $this->filters) && !empty($this->filters['withdrawal_id'])) {

            $withdrawal_id = $this->filters['withdrawal_id'];

            $pendingDebts = PendingDebt::select('pending_debts.*')
                ->join('pending_debt_withdrawals', function ($j) use ($withdrawal_id) {
                    return $j->on('pending_debt_withdrawals.pending_debt_id', '=', 'pending_debts.id')
                        ->where('pending_debt_withdrawals.withdrawal_id', $withdrawal_id);
                })
                ->whereIn('type', ['ADJUSTMENT', 'REVERSED'])
                ->whereCompanyId($companyId)
                ->get();

        } else {

            if ($this->filters['status'] == 'PENDING_DEBIT') {

                $pendingDebts = PendingDebt::doesntHave('withdrawals')
                    ->whereNull('confirm_date')
                    ->whereCompanyId($companyId)
                    ->get();
            }
        }

        foreach ($pendingDebts as $pendingDebt) {

            $amount = $pendingDebt->value / 100;
            $amount = $amount * -1;

            $paymentDate = $pendingDebt->payment_date ? Carbon::createFromFormat('Y-m-d',
                $pendingDebt->payment_date)->format('d/m/Y') : '';
            $transactionDate = $pendingDebt->request_date ? Carbon::createFromFormat('Y-m-d H:i:s',
                $pendingDebt->request_date)->format('d/m/Y') : '';
            $subSellerRateConfirmDate = $pendingDebt->confirm_date ? Carbon::createFromFormat('Y-m-d',
                $pendingDebt->confirm_date)->format('d/m/Y') : '';
            $sequence = $pendingDebt->payment_date ? Carbon::createFromFormat('Y-m-d',
                $pendingDebt->payment_date)->format('Ymd') : 0;

            $details = new Details();

            if ($pendingDebt->type == 'ADJUSTMENT') {

                $type = StatementItem::TYPE_ADJUSTMENT;
                $details->setStatus('Ajuste de débito')
                    ->setDescription($pendingDebt->reason ?? '')
                    ->setType(Details::STATUS_ADJUSTMENT_DEBIT);
            } else {

                $type = StatementItem::TYPE_REVERSED;
                $details->setStatus('Estornado')
                    ->setDescription('Solicitação do estorno: ' . $transactionDate)
                    ->setType(Details::STATUS_REVERSED);
            }

            $orderFromGetNetOrderId = null;

            if ($pendingDebt->sale_id) {

                $sale = Sale::select('gateway_order_id')->find($pendingDebt->sale_id);
                $gateway_order_id = $sale->gateway_order_id;
                $orderFromGetNetOrderId = $this->setOrderFromGetNetOrderId($gateway_order_id);
            }

            $statementItem = new StatementItem();

            $statementItem->amount = $amount;
            $statementItem->order = $orderFromGetNetOrderId;
            $statementItem->details = $details;
            $statementItem->type = $type;
            $statementItem->transactionDate = $pendingDebt->adjustment_date ?? '';
            $statementItem->date = '';//$paymentDate; // TODO Rupert em 02/03/2021: A pedido do Julio
            $statementItem->subSellerRateConfirmDate = $subSellerRateConfirmDate;
            $statementItem->sequence = $sequence;

            $this->totalInPeriod += $amount;
            $this->totalChargeback += $amount;
            $this->statementItems[] = $statementItem;
        }
    }

    public function performStatement(stdClass $data, array $filters = [])
    {

        if (isset($data->errors)) {

            //dd($data->errors);
            $exception = new Exception('Houve um erro ao processar a requisição na getnet em ' . __METHOD__ . ' :: ' . $data->errors[0]->message);
            report($exception);
        }

        $this->filters = $filters;

        $transactions = array_reverse($data->list_transactions ?? []);
        $adjustments = array_reverse($data->adjustments ?? []);
        //$chargeback = array_reverse($data->chargeback ?? []);

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

    public function getFiltersAndStatement($companyId)
    {
        $credential = GatewaysCompaniesCredential::where('company_id',$companyId)
                                ->where('gateway_id',FoxUtils::isProduction() ? Gateway::GETNET_PRODUCTION_ID : Gateway::GETNET_SANDBOX_ID)
                                ->where('gateway_status',GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED)
                                ->with('company',function($qr){
                                    $qr->where('user_id', auth()->user()->account_owner_id);
                                })->first();

        if (empty($credential)) {
            return response()->json([]);
        }

        try {
            $dates = explode(' - ', request('dateRange') ?? '');

            $startDate = Carbon::createFromFormat('d/m/Y', $dates[0]);
            $endDate = Carbon::createFromFormat('d/m/Y', $dates[1]);
        } catch (Exception $exception) {
        }

        if (!isset($startDate) || !isset($endDate)) {
            $today = today();
            $startDate = $today;
            $endDate = $today;
        }

        switch(request('statement_data_type')){
            case 'schedule_date':
                $statementDateField = GetnetBackOfficeService::STATEMENT_DATE_SCHEDULE;
            break;
            case 'liquidation_date':
                $statementDateField = GetnetBackOfficeService::STATEMENT_DATE_LIQUIDATION;
            break;
            default:
                $statementDateField = GetnetBackOfficeService::STATEMENT_DATE_TRANSACTION;
            break;
        }

        $getNetBackOfficeService = new GetnetBackOfficeService();
        $getNetBackOfficeService->setStatementSubSellerId($credential->gateway_subseller_id)
            ->setStatementStartDate($startDate)
            ->setStatementEndDate($endDate)
            ->setStatementDateField($statementDateField);

        if (!empty(request('sale'))) {

            $getNetBackOfficeService->setStatementSaleHashId(request('sale'));
            $filters['sale'] = request('sale');
        }

        $statement = $getNetBackOfficeService->getStatement();

        // Só vai ser true após a execução de $getNetBackOfficeService->getStatement()
        if ($getSaleId = $getNetBackOfficeService->getSaleId()) {

            $filters['sale_id'] = $getSaleId;
        }

        $filters['start_date'] = $startDate;
        $filters['end_date'] = $endDate;
        $filters['company_id'] = $credential->company_id;
        $filters['status'] = request()->get('status');
        $filters['payment_method'] = request()->get('payment_method');
        $filters['withdrawal_id'] = current(Hashids::decode(request()->get('withdrawal_id')));

        return [
            'filters' => $filters,
            'statement' => $statement,
        ];
    }

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

    private function preparesNodeChargeback(): void
    {

    }
}
