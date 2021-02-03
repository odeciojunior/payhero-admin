<?php

namespace Modules\Core\Services;

use App\Jobs\GetnetGetDiscountsJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use LogicException;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\PendingDebt;
use Modules\Core\Entities\Sale;
use Modules\Core\Traits\GetnetPrepareCompanyData;
use Modules\Transfers\Getnet\Details;
use Modules\Transfers\Getnet\StatementItem;
use Modules\Transfers\Services\GetNetStatementService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class GetnetService
 * @package Modules\Core\Services
 */
class GetnetBackOfficeService extends GetnetService
{
    use GetnetPrepareCompanyData;

    public const STATEMENT_DATE_SCHEDULE = 'schedule';
    public const STATEMENT_DATE_TRANSACTION = 'transaction';
    public const STATEMENT_DATE_LIQUIDATION = 'liquidation';

    private string $urlCredentialAccessToken = 'credenciamento/auth/oauth/v2/token';
    public string $postFieldsAccessToken, $authorizationToken;
    protected ?string $sellerId, $statementSubSellerId = null;
    protected Carbon $statementStartDate, $statementEndDate;
    protected ?string $statementDateField, $statementSaleHashId = '';
    protected int $statementPage = 1;

    /**
     * @return string|null
     */
    public function getStatementSubSellerId(): ?string
    {
        return $this->statementSubSellerId;
    }

    /**
     * @param string|null $statementSubSellerId
     * @return GetnetBackOfficeService
     */
    public function setStatementSubSellerId(?string $statementSubSellerId): GetnetBackOfficeService
    {
        $this->statementSubSellerId = $statementSubSellerId;
        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getStatementStartDate(): ?Carbon
    {
        return $this->statementStartDate ?? null;
    }

    /**
     * @param Carbon $statementStartDate
     * @return GetnetBackOfficeService
     */
    public function setStatementStartDate(Carbon $statementStartDate): GetnetBackOfficeService
    {
        $this->statementStartDate = $statementStartDate;
        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getStatementEndDate(): ?Carbon
    {
        return $this->statementEndDate ?? null;
    }

    /**
     * @param Carbon $statementEndDate
     * @return GetnetBackOfficeService
     */
    public function setStatementEndDate(Carbon $statementEndDate): GetnetBackOfficeService
    {
        $this->statementEndDate = $statementEndDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatementDateField(): ?string
    {
        return $this->statementDateField ?? null;
    }

    /**
     * @param string|null $statementDateField
     * @return GetnetBackOfficeService
     */
    public function setStatementDateField(?string $statementDateField): GetnetBackOfficeService
    {
        $this->statementDateField = $statementDateField;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatementSaleHashId(): ?string
    {
        return $this->statementSaleHashId ?? null;
    }

    /**
     * @param string|null $statementSaleHashId
     * @return GetnetBackOfficeService
     */
    public function setStatementSaleHashId(?string $statementSaleHashId): GetnetBackOfficeService
    {
        $this->statementSaleHashId = $statementSaleHashId;
        return $this;
    }

    public function __construct()
    {
        try {
            $this->setAuthorizationToken();

            $this->setAccessToken($this->urlCredentialAccessToken, $this->postFieldsAccessToken);

            $this->setSellerId();
        } catch (Exception $e) {
            report($e);
        }

        parent::__construct();
    }

    private function setAuthorizationToken()
    {
        if (FoxUtils::isProduction()) {
            $this->authorizationToken = base64_encode(
                getenv('GET_NET_CLIENT_ID_PRODUCTION') . ':' . getenv('GET_NET_CLIENT_SECRET_PRODUCTION')
            );

            $this->postFieldsAccessToken = 'scope=oob&grant_type=client_credentials';
        } else {
            $this->authorizationToken = base64_encode(
                getenv('GET_NET_CLIENT_ID_SANDBOX') . ':' . getenv('GET_NET_CLIENT_SECRET_SANDBOX')
            );

            $this->postFieldsAccessToken = 'scope=mgm&grant_type=client_credentials';
        }
    }

    public function getMerchantId()
    {
        if (FoxUtils::isProduction()) {
            return env('GET_NET_MERCHANT_ID_PRODUCTION');
        }

        return env('GET_NET_MERCHANT_ID_SANDBOX');
    }

    public function getAuthorizationHeader()
    {
        return [
            'authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
        ];
    }

    public function setSellerId()
    {
        if (FoxUtils::isProduction()) {
            $this->sellerId = getenv('GET_NET_SELLER_ID_PRODUCTION');
        } else {
            $this->sellerId = getenv('GET_NET_SELLER_ID_SANDBOX');
        }
    }

    public function getAdjustments(Company $company)
    {

        $totalAdjustment = 0;
        $statementItems = [];

        if (FoxUtils::isProduction()) {

            $subSellerId = $company->subseller_getnet_id;
        } else {

            $subSellerId = $company->subseller_getnet_homolog_id;
        }

        /*$withdrawals = Withdrawal::whereCompanyId($company->id)
            ->get();*/

        $dateInit = '2020-12-01 00:00:00';
        $dateEnd = today()->addDays(7)->format('Y-m-d') . ' 23:59:59';

        $queryParameters = [
            'seller_id' => $this->sellerId,
            'subseller_id' => $subSellerId,
            'schedule_date_init' => $dateInit,
            'schedule_date_end' => $dateEnd,
        ];

        $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
        $originalResult = $this->sendCurl($url, 'GET', null, null, false);
        $result = json_decode($originalResult);

        if (isset($result->adjustments)) {

            foreach ($result->adjustments as $adjustment) {

                if (
                    $adjustment->cnpj_marketplace != $adjustment->cpfcnpj_subseller
                    && $adjustment->transaction_sign == '-'
                    && empty($adjustment->subseller_rate_confirm_date)
                ) {

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

                    $statementItem = new StatementItem();

                    $statementItem->amount = $amount;
                    $statementItem->details = $details;
                    $statementItem->type = StatementItem::TYPE_ADJUSTMENT;
                    $statementItem->transactionDate = $adjustmentDate;
                    $statementItem->date = $date;
                    $statementItem->subSellerRateConfirmDate = $subSellerRateConfirmDate;
                    $statementItem->sequence = $statementItem->date ? (Carbon::createFromFormat('d/m/Y',
                        $statementItem->date)->format('Ymd')) : 0;

                    $totalAdjustment += $amount;
                    $statementItems[] = $statementItem;

                }
            }
        }

        return [
            'amount' => $totalAdjustment,
            'items' => $statementItems,
        ];
    }

    public function saveDiscountsInDatabase(Company $company): array
    {

        $total = 0;
        $totalReversed = 0;
        $totalAdjustment = 0;
        $statementItems = [];

        if (FoxUtils::isProduction()) {

            $subSellerId = $company->subseller_getnet_id;
        } else {

            $subSellerId = $company->subseller_getnet_homolog_id;
        }

        $dateInit = '2020-07-01 00:00:00';
        $dateEnd = today()->addDays(7)->format('Y-m-d') . ' 23:59:59';

        $queryParameters = [
            'seller_id' => $this->sellerId,
            'subseller_id' => $subSellerId,
            'schedule_date_init' => $dateInit,
            'schedule_date_end' => $dateEnd,
        ];

        $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
        $originalResult = $this->sendCurl($url, 'GET', null, null, false);
        $result = json_decode($originalResult);

        if (isset($result->list_transactions)) {

            foreach ($result->list_transactions as $item) {

                if (isset($item->summary) && isset($item->details) && is_array($item->details) && count($item->details) == 1) {

                    $summary = $item->summary;
                    $details = $item->details[0];

                    $amount = $details->subseller_rate_amount / 100;
                    $amount = $details->transaction_sign == '-' ? ($amount * -1) : $amount;

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

                    $transactionStatusCode = $summary->transaction_status_code;
                    // Aqui existe redundância para ficar mais legível quando comparado com o código do GetNetStatementService.php
                    if ($transactionStatusCode == GetNetStatementService::TRANSACTION_STATUS_CODE_ESTORNADA) {

                        $paidWith = null;
                        $type = StatementItem::TYPE_REVERSED;

                        $orderFromGetNetOrderId = (new GetNetStatementService)->setOrderFromGetNetOrderId($summary->order_id);
                        $hasOrderId = empty($summary->order_id) ? false : true;
                        $isTransactionCredit = $details->transaction_sign == '+';

                        if ($hasOrderId && !$isTransactionCredit && $transactionStatusCode == GetNetStatementService::TRANSACTION_STATUS_CODE_APROVADO) {

                            $details = new Details();
                            $details->setStatus('Estornado')
                                ->setDescription('Solicitação do estorno: ' . $this->formatDate($summary->transaction_date))
                                ->setType(Details::STATUS_REVERSED);

                            $date = $subSellerRateConfirmDate ? $subSellerRateClosingDate : $paymentDate;

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

                            $total += $amount;
                            $totalReversed += $amount;
                            $statementItems[] = $statementItem;

                        }
                    }
                }
            }
        }

        if (isset($result->adjustments)) {

            foreach ($result->adjustments as $adjustment) {

                if (
                    $adjustment->cnpj_marketplace != $adjustment->cpfcnpj_subseller
                    && $adjustment->transaction_sign == '-'
                    //&& empty($adjustment->subseller_rate_confirm_date)
                ) {

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

                    $statementItem = new StatementItem();

                    $statementItem->amount = $amount;
                    $statementItem->details = $details;
                    $statementItem->type = StatementItem::TYPE_ADJUSTMENT;
                    $statementItem->transactionDate = $adjustmentDate;
                    $statementItem->date = $date;
                    $statementItem->subSellerRateConfirmDate = $subSellerRateConfirmDate;
                    $statementItem->sequence = $statementItem->date ? (Carbon::createFromFormat('d/m/Y',
                        $statementItem->date)->format('Ymd')) : 0;

                    $totalAdjustment += $amount;
                    $total += $amount;
                    $statementItems[] = $statementItem;

                }
            }
        }

        $items = collect($statementItems)->sortByDesc('sequence')->values()->all();

        foreach ($items as $item) {

            PendingDebt::updateOrCreate([
                'company_id' => $company->id,
                'sale_id' => $item->order ? $item->order->getSaleId() : null,
                'type' => $item->type,
                'value' => abs($item->amount * 100),
            ],
                [
                    'request_date' => $item->transactionDate ? Carbon::createFromFormat('d/m/Y',
                        $item->transactionDate) : null,
                    'confirm_date' => $item->subSellerRateConfirmDate ? Carbon::createFromFormat('d/m/Y',
                        $item->subSellerRateConfirmDate) : null,
                    'payment_date' => $item->date ? Carbon::createFromFormat('d/m/Y', $item->date) : null,
                    'reason' => $item->details->getDescription(),
                ]);
        }

        $return = [
            'amount' => $total,
            'amount_adjustments' => $totalAdjustment,
            'amount_reversed' => $totalReversed,
            'items' => $items,
        ];

        Log::debug(json_encode([
            'method' => __METHOD__,
            'user_id' => auth()->id() ?? null,
            'company_id' => $company->id,
            'return' => $return,
        ]));

        Redis::connection('redis-statement')->set("getDiscounts:lastVerification:" . $company->id, date('YmdHi'));

        return $return;
    }

    public function getDiscounts(Company $company)
    {

        $total = 0;
        $totalReversed = 0;
        $totalAdjustment = 0;
        $statementItems = [];

        if (FoxUtils::isProduction()) {

            $subSellerId = $company->subseller_getnet_id;
        } else {

            $subSellerId = $company->subseller_getnet_homolog_id;
        }

        $dateInit = '2020-12-01 00:00:00';
        $dateEnd = today()->addDays(7)->format('Y-m-d') . ' 23:59:59';

        $queryParameters = [
            'seller_id' => $this->sellerId,
            'subseller_id' => $subSellerId,
            'schedule_date_init' => $dateInit,
            'schedule_date_end' => $dateEnd,
        ];

        $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
        $originalResult = $this->sendCurl($url, 'GET', null, null, false);
        $result = json_decode($originalResult);

        if (isset($result->list_transactions)) {

            foreach ($result->list_transactions as $item) {

                if (isset($item->summary) && isset($item->details) && is_array($item->details) && count($item->details) == 1) {

                    $summary = $item->summary;
                    $details = $item->details[0];

                    if (empty($details->subseller_rate_confirm_date)) {

                        $amount = $details->subseller_rate_amount / 100;
                        $amount = $details->transaction_sign == '-' ? ($amount * -1) : $amount;

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

                        $transactionStatusCode = $summary->transaction_status_code;
                        // Aqui existe redundância para ficar mais legível quando comparado com o código do GetNetStatementService.php
                        if ($transactionStatusCode == GetNetStatementService::TRANSACTION_STATUS_CODE_ESTORNADA) {

                            $paidWith = null;
                            $type = StatementItem::TYPE_REVERSED;

                            $orderFromGetNetOrderId = (new GetNetStatementService)->setOrderFromGetNetOrderId($summary->order_id);
                            $hasOrderId = empty($summary->order_id) ? false : true;
                            $isTransactionCredit = $details->transaction_sign == '+';

                            if ($hasOrderId && !$isTransactionCredit && $transactionStatusCode == GetNetStatementService::TRANSACTION_STATUS_CODE_APROVADO) {

                                $details = new Details();
                                $details->setStatus('Estornado')
                                    ->setDescription('Solicitação do estorno: ' . $this->formatDate($summary->transaction_date))
                                    ->setType(Details::STATUS_REVERSED);

                                $date = $subSellerRateConfirmDate ? $subSellerRateClosingDate : $paymentDate;

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

                                $total += $amount;
                                $totalReversed += $amount;
                                $statementItems[] = $statementItem;

                            }

                        }
                    }

                }
            }
        }

        if (isset($result->adjustments)) {

            foreach ($result->adjustments as $adjustment) {

                if (
                    $adjustment->cnpj_marketplace != $adjustment->cpfcnpj_subseller
                    && $adjustment->transaction_sign == '-'
                    && empty($adjustment->subseller_rate_confirm_date)
                ) {

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

                    $statementItem = new StatementItem();

                    $statementItem->amount = $amount;
                    $statementItem->details = $details;
                    $statementItem->type = StatementItem::TYPE_ADJUSTMENT;
                    $statementItem->transactionDate = $adjustmentDate;
                    $statementItem->date = $date;
                    $statementItem->subSellerRateConfirmDate = $subSellerRateConfirmDate;
                    $statementItem->sequence = $statementItem->date ? (Carbon::createFromFormat('d/m/Y',
                        $statementItem->date)->format('Ymd')) : 0;

                    $totalAdjustment += $amount;
                    $total += $amount;
                    $statementItems[] = $statementItem;

                }
            }
        }

        return [
            'amount' => $total,
            'amount_adjustments' => $totalAdjustment,
            'amount_reversed' => $totalReversed,
            'items' => collect($statementItems)->sortByDesc('sequence')->values()->all(),
        ];
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

    /**
     * Endpoint para solicitação de extrato eletrônico
     * @method GET
     * @return bool|string
     */
    public function getStatement()
    {
        if (empty($this->getStatementDateField()) && empty($this->getStatementSaleHashId())) {
            throw new LogicException('É obrigatório especificar um campo de data para a busca quando não é enviado um OrderId');
        } elseif (!empty($this->getStatementDateField()) && !in_array($this->getStatementDateField(),
                [self::STATEMENT_DATE_SCHEDULE, self::STATEMENT_DATE_LIQUIDATION, self::STATEMENT_DATE_TRANSACTION])) {
            throw new LogicException('O campo de data para a busca deve ser "' . self::STATEMENT_DATE_SCHEDULE . '", "' . self::STATEMENT_DATE_LIQUIDATION . '" ou "' . self::STATEMENT_DATE_TRANSACTION . '"');
        }

        $queryParameters = [
            'seller_id' => $this->sellerId,
        ];

        if ($this->getStatementStartDate() && $this->getStatementEndDate()) {

            $startDate = $this->getStatementStartDate()->format('Y-m-d');
            $endDate = $this->getStatementEndDate()->format('Y-m-d');

            $startDate .= ' 00:00:00';
            $endDate .= ' 23:59:59';

            $queryParameters[$this->getStatementDateField() . '_date_init'] = $startDate;
            $queryParameters[$this->getStatementDateField() . '_date_end'] = $endDate;
        }

        if (!empty($this->getStatementSubSellerId())) {

            $queryParameters['subseller_id'] = $this->getStatementSubSellerId();
        }

        if (!empty($this->getStatementSaleHashId())) {
            $sale = Sale::find(current(Hashids::connection('sale_id')->decode($this->getStatementSaleHashId())));

            /*if ($sale) {
                if ($sale->created_at > '2020-10-30 13:28:51.0') {
                    $orderId = $this->getStatementSaleHashId() . '-' . $sale->id . '-' . $sale->attempts;
                } else {
                    $orderId = $this->getStatementSaleHashId() . '-' . $sale->attempts;
                }

                $queryParameters['order_id'] = $orderId;
            }*/

            if ($sale) {

                $queryParameters['order_id'] = $sale->gateway_order_id;
            }
        }


        if (request('debug')) {

            echo '<pre>';
            print_r($queryParameters);
            echo '</pre>';
            echo '<hr>';
        }

        $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
        //$url = 'v1/mgm/statement/get-paginated-statement?' . http_build_query($queryParameters);
        return $this->sendCurl($url, 'GET', null, null, false);
    }

    public function checkPfCompanyRegister(string $cpf, $companyId)
    {
        $url = 'v1/mgm/pf/callback/' . $this->getMerchantId() . '/' . $cpf;

        return $this->sendCurl($url, 'GET', null, $companyId);
    }

    public function checkAvailablePaymentPlansPf()
    {
        $url = 'v1/mgm/pf/consult/paymentplans/' . $this->getMerchantId();

        return $this->sendCurl($url, 'GET');
    }

    public function createPfCompany(Company $company)
    {
        $url = 'v1/mgm/pf/create-presubseller';
        $data = $this->getPrepareDataCreatePfCompany($company);

        return $this->sendCurl($url, 'POST', $data, $company->id);
    }

    public function complementPfCompany(Company $company)
    {
        $url = 'v1/mgm/pf/complement';
        $data = $this->getPrepareDataComplementPfCompany($company);

        return $this->sendCurl($url, 'PUT', $data, $company->id);
    }

    public function disqualifyPfCompany($subsellerGetnetId)
    {
        $url = 'v1/mgm/pf/de-accredit/' . $this->getMerchantId() . '/' . $subsellerGetnetId;

        return $this->sendCurl($url, 'POST');
    }

    public function updatePfCompany(Company $company, $dataUpdate)
    {
        $data = array_merge($this->getPrepareDataUpdatePfCompany($company), $dataUpdate);

        return $this->sendCurl('v1/mgm/pf/update-subseller', 'PUT', $data, $company->id);
    }

    public function checkPaymentPlans()
    {
        $url = "v1/mgm/pj/consult/paymentplans/{$this->getMerchantId()}";

        return $this->sendCurl($url, 'GET');
    }

    public function checkComplementPjCompanyRegister($cnpj)
    {
        $url = 'v1/mgm/pj/consult/' . $this->getMerchantId() . '/' . $cnpj;

        return $this->sendCurl($url, 'GET');
    }

    public function checkPjCompanyRegister($cnpj, $companyId)
    {
        $url = 'v1/mgm/pj/callback/' . $this->getMerchantId() . '/' . $cnpj;

        return $this->sendCurl($url, 'GET', null, $companyId);
    }

    public function checkAvailablePaymentPlansPj()
    {
        $url = 'v1/mgm/pj/consult/paymentplans/' . $this->getMerchantId();

        return $this->sendCurl($url, 'GET');
    }

    public function createPjCompany(Company $company)
    {
        $url = 'v1/mgm/pj/create-presubseller';
        $data = $this->getDataToCreatePjCompany($company);

        return $this->sendCurl($url, 'POST', $data, $company->id);
    }

    public function complementPjCompany(Company $company)
    {
        $url = 'v1/mgm/pj/complement';
        $data = $this->getPrepareDataComplementPjCompany($company);

        return $this->sendCurl($url, 'PUT', $data, $company->id);
    }

    public function updatePjCompany(Company $company, $dataUpdate)
    {
        $data = array_merge($this->getPrepareDataUpdatePjCompany($company), $dataUpdate);

        return $this->sendCurl('v1/mgm/pj/update-subseller', 'PUT', $data, $company->id);
    }

    public function disqualifyPjCompany($subsellerGetnetId)
    {
        $url = 'v1/mgm/pj/de-accredit/' . $this->getMerchantId() . '/' . $subsellerGetnetId;

        return $this->sendCurl($url, 'POST');
    }

    public function getStatementFromManager(array $filters = [], int $page = null)
    {
        $queryParameters = $filters;
        $queryParameters['seller_id'] = $this->sellerId;

        if (is_null($page)) {
            $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
        } else {
            $queryParameters['page'] = $page;
            $url = 'v1/mgm/paginatedstatement?' . http_build_query($queryParameters);
        }

        return $this->sendCurl($url, 'GET');
    }

    public static function dispatchGetnetGetDiscountsJob()
    {

        $companies = Auth::user()->companies;

        foreach ($companies as $company) {

            if ($company->get_net_status == 1) {

                $lastVerification = Redis::connection('redis-statement')->get("getDiscounts:lastVerification:" . $company->id);

                $timeLimit = 120;
                $now = date('YmdHi');

                if (!$lastVerification || ($now - $lastVerification > $timeLimit)) {

                    Log::info(json_encode([
                        'method' => __METHOD__,
                        'user_id' => auth()->id() ?? null,
                        'company_id' => $company->id,
                        'action' => 'GetnetGetDiscountsJob::dispatch()',
                        'lastVerification' => $lastVerification,
                        'date' => $now,
                    ]));

                    GetnetGetDiscountsJob::dispatch($company);
                } else {

                    Log::notice(json_encode([
                        'method' => __METHOD__,
                        'user_id' => auth()->id() ?? null,
                        'company_id' => $company->id,
                        'action' => 'NONE',
                        'lastVerification' => $lastVerification,
                        'date' => $now,
                    ]));
                }
            }
        }

    }

}
