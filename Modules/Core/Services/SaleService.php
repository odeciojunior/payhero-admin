<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\PendingDebt;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Entities\SaleWhiteBlackListResult;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Events\BilletRefundedEvent;
use Modules\Products\Transformers\ProductsSaleResource;
use Modules\Transfers\Services\GetNetStatementService;
use PagarMe\Client as PagarmeClient;
use Vinkla\Hashids\Facades\Hashids;

class SaleService
{
    public function getPaginatedSales($filters)
    {
        $transactions = $this->getSalesQueryBuilder($filters);

        return $transactions->paginate(10);
    }

    public function getSalesQueryBuilder($filters, $withProducts = false, $userId = 0, $withCashback = false)
    {
        try {
            $companyModel = new Company();
            $customerModel = new Customer();
            $transactionModel = new Transaction();
            $couponModel = new DiscountCoupon();

            if (!$userId) {
                $userId = auth()->user()->account_owner_id;
            }

            if (empty($filters["company"])) {
                $userCompanies = $companyModel->where('user_id', $userId)
                    ->get()
                    ->pluck('id')
                    ->toArray();

            } else {
                $userCompanies = [];
                $companies = explode(',', $filters["company"]);

                foreach($companies as $company){
                    array_push($userCompanies, current(Hashids::decode($company)));
                }

            }

            $relationsArray = [
                'sale',
                'sale.project',
                'sale.customer',
                'sale.plansSales',
                'sale.shipping',
                'sale.checkout',
                'sale.delivery',
                'sale.affiliate.user',
                'sale.saleRefundHistory',
                'sale.cashback'
            ];

            if ($withProducts) {
                $relationsArray[] = 'sale.productsPlansSale.plan';
                $relationsArray[] = 'sale.productsPlansSale.product';
            }

            $transactions = $transactionModel->with($relationsArray)
                ->whereIn('company_id', $userCompanies)
                ->join('sales', 'sales.id', 'transactions.sale_id')
                ->whereNull('invitation_id');

            if (!$withCashback) {
                $transactions->where('type', '<>', $transactionModel->present()->getType('cashback'));
            }

            if (!empty($filters["project"])) {
                $projectIds =[];
                $projects = explode(',', $filters["project"]);

                foreach($projects as $project){
                    array_push($projectIds, current(Hashids::decode($project)));
                }

                //$projectId = current(Hashids::decode($filters["project"]));
                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($projectIds) {
                        $querySale->whereIn('project_id', $projectIds);
                    }
                );
            }

            if (!empty($filters["transaction"])) {
                $saleId = current(
                    Hashids::connection('sale_id')->decode(str_replace('#', '', $filters["transaction"]))
                );

                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($saleId) {
                        $querySale->where('id', $saleId);
                    }
                );
            }

            if (!empty($filters["client"]) && empty($filters["email_client"])) {
                $customers = $customerModel->where('name', 'LIKE', '%' . $filters["client"] . '%')->pluck('id');
                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($customers) {
                        $querySale->whereIn('customer_id', $customers);
                    }
                );
            }

            if (!empty($filters['customer_document'])) {
                $customers = $customerModel->where(
                    'document',
                    FoxUtils::onlyNumbers($filters["customer_document"])
                )->pluck('id');

                if (count($customers) < 1) {
                    $customers = $customerModel->where('document', $filters["customer_document"])->pluck('id');
                }

                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($customers) {
                        $querySale->whereIn('customer_id', $customers);
                    }
                );
            }

            // novo filtro
            if (!empty($filters["coupon"])) {
                $couponCode = $filters["coupon"];

                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($couponCode) {
                        $querySale->where('cupom_code', 'LIKE', '%' . $couponCode . '%');
                    }
                );
            }

            // novo filtro
            if (!empty($filters["value"])) {
                $value = $filters["value"];

                $transactions->where('value', $value);
            }

            // novo filtro
            if (!empty($filters["email_client"]) && empty($filters["client"])) {
                $customers = $customerModel->where('email', 'LIKE', '%' . $filters["email_client"] . '%')->pluck('id');
                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($customers) {
                        $querySale->whereIn('customer_id', $customers);
                    }
                );
            }

            // novo filtro
            if (!empty($filters["email_client"]) && !empty($filters["client"])) {
                $customers = $customerModel->where('name', 'LIKE', '%' . $filters["client"] . '%')->where(
                    'email',
                    'LIKE',
                    '%' . $filters["email_client"] . '%'
                )->pluck('id');
                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($customers) {
                        $querySale->whereIn('customer_id', $customers);
                    }
                );
            }


            if (!empty($filters['shopify_error']) && $filters['shopify_error'] == true) {
                $transactions->whereHas(
                    'sale.project.shopifyIntegrations',
                    function ($queryShopifyIntegration) {
                        $queryShopifyIntegration->where('status', 2);
                    }
                );
                $transactions->whereHas(
                    'sale',
                    function ($querySaleShopify) {
                        $querySaleShopify->whereNull('shopify_order')->where(
                            'start_date',
                            '<=',
                            Carbon::now()->subMinutes(5)
                                ->toDateTimeString()
                        );
                    }
                );
            }
            if (!empty($filters["payment_method"])) {
                $forma = $filters["payment_method"];
                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($forma) {
                        $querySale->whereIn('payment_method', explode(',', $forma));
                    }
                );
            }

            if (!empty($filters["plan"])) {
                $planIds = [];
                $plans = explode(',', $filters["plan"]);

                foreach($plans as $plan){
                    array_push($planIds, current(Hashids::decode($plan)));
                }
                // $planId = current(Hashids::decode($filters["plan"]));

                $transactions->whereHas(
                    'sale.plansSales',
                    function ($query) use ($planIds) {
                        $query->whereIn('plan_id', $planIds);
                    }
                );
            }

            if (empty($filters['status'])) {
                $status = [1, 2, 4, 7, 8, 12, 20, 21, 22, 24];
            } else {
                $status = explode(',', $filters['status']);
                //$status = in_array(7, $status) ? [7, 22] : $status; //REMOVER ESTA LINHA PARA APARECER TODOS OS STATUS
            }

            if(!empty($status)) {
                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($status) {
                        $querySale->whereIn('status', $status);
                    }
                );
            }
            if (!empty($filters['upsell']) && $filters['upsell'] == true) {
                $transactions->whereHas(
                    'sale',
                    function ($querySale) {
                        $querySale->whereNotNull('upsell_id');
                    }
                );
            }

            if (!empty($filters["cashback"])) {
                $transactions->whereHas(
                    'sale',
                    function ($querySale) {
                        $querySale->whereHas('cashback');
                    }
                );
            }

            if (!empty($filters['order_bump']) && $filters['order_bump'] == true) {
                $transactions->whereHas(
                    'sale',
                    function ($querySale) {
                        $querySale->where('has_order_bump', true);
                    }
                );
            }

            //tipo da data e periodo obrigatorio
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $dateType = $filters["date_type"];

            $transactions->whereHas(
                'sale',
                function ($querySale) use ($dateRange, $dateType) {
                    $querySale->whereBetween($dateType, [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
                }
            )->selectRaw('transactions.*, sales.start_date')
                ->orderByDesc('sales.start_date');

            return $transactions;
        } catch (Exception $e) {
            report($e);
            return null;
        }
    }

    public function getResume($filters)
    {
        $transactionModel = new Transaction();
        $transactions = $this->getSalesQueryBuilder($filters, false, null, true);
        $transactionStatus = implode(
            ',',
            [
                $transactionModel->present()->getStatusEnum('paid'),
                $transactionModel->present()
                    ->getStatusEnum('transfered'),
            ]
        );
        $statusDispute = (new Sale())->present()->getStatus('in_dispute');

        $resume = $transactions->without(['sale'])
            ->select(
                DB::raw(
                    "count(sales.id) as total_sales,
                              sum(if(transactions.status_enum in ({$transactionStatus}) && sales.status <> {$statusDispute}, transactions.value, 0)) / 100 as commission,
                              sum((sales.sub_total + sales.shipment_value) - (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100) as total"
                )
            )
            ->first()
            ->toArray();

        $resume['commission'] = number_format($resume['commission'], 2, ',', '.');
        $resume['total'] = number_format($resume['total'], 2, ',', '.');

        return $resume;
    }

    public function getSaleWithDetails($saleId)
    {
        $companyModel = new Company();
        $saleModel = new Sale();

        //get sale
        $sale = $saleModel->with(
            [
                'transactions',
                'notazzInvoices',
                'affiliate',
                'saleRefundHistory'
            ]
        )->find(current(Hashids::connection('sale_id')->decode($saleId)));

        //add details to sale
        $userCompanies = $companyModel->where('user_id', $sale->owner_id)->pluck('id');

        $this->getDetails($sale, $userCompanies);

        //invoices
        $invoices = [];
        foreach ($sale->notazzInvoices as $notazzInvoice) {
            $invoices[] = Hashids::encode($notazzInvoice->id);
        }
        $sale->details->invoices = $invoices;

        return $sale;
    }

    public function getDetails($sale, $userCompanies)
    {
        $userTransaction = $sale->transactions->where('invitation_id', null)
            ->whereIn('company_id', $userCompanies)
            ->first();

        //calcule total
        $subTotal = FoxUtils::onlyNumbers($sale->sub_total);

        $total = $subTotal;

        $shipment_value = FoxUtils::onlyNumbers($sale->shipment_value);
        $total += $shipment_value;
        $sale->shipment_value = number_format(intval($shipment_value) / 100, 2, ',', '.');

        if (FoxUtils::onlyNumbers($sale->shopify_discount) > 0) {
            $total -= FoxUtils::onlyNumbers($sale->shopify_discount);
            $discount = FoxUtils::onlyNumbers($sale->shopify_discount);
        } else {
            $discount = '0,00';
        }

        $total -= $sale->automatic_discount;

        //valor do produtor
        $value = $userTransaction->value??0;
        $cashbackValue = $sale->cashback->value ?? 0;
        $comission = 'R$ ' . substr_replace($value, ',', strlen($value) - 2, 0);

        //valor do afiliado
        $affiliateComission = '';
        $affiliateValue = 0;
        if (!empty($sale->affiliate_id)) {
            $affiliate = Affiliate::withTrashed()->find($sale->affiliate_id);
            $affiliateTransaction = $sale->transactions->where('company_id', $affiliate->company_id)->first();
            if (!empty($affiliateTransaction)) {
                $affiliateValue = $affiliateTransaction->value;
                $affiliateComission = 'R$ ' . number_format($affiliateValue / 100, 2, ',', '.');
            }
        }

        $taxa = $totalTaxPercentage = $totalTax = $transactionRate = 0;
        //$totalToCalcTaxReal = ($sale->present()->getStatus() == 'refunded') ? $total + $sale->refund_value : $total;
        $totalToCalcTaxReal = $total + $cashbackValue;

        if ($userTransaction->percentage_rate > 0) {
            $totalTaxPercentage = (int)($totalToCalcTaxReal * ($userTransaction->percentage_rate / 100));
            $totalTax += $totalTaxPercentage;
        }

        if ($userTransaction->transaction_rate > 0) {
            $transactionRate = FoxUtils::onlyNumbers($userTransaction->transaction_rate);
            $totalTax += $transactionRate;
        }


        if (FoxUtils::onlyNumbers($sale->installment_tax_value) > 0) {
            $taxaReal = $totalToCalcTaxReal
                - FoxUtils::onlyNumbers($comission)
                - FoxUtils::onlyNumbers($sale->installment_tax_value);
        } else {
            $taxaReal = $totalToCalcTaxReal - FoxUtils::onlyNumbers($comission);
        }

        if ($taxaReal < 0) {
            $taxaReal *= -1;
        }

        if (!empty($sale->affiliate_id) && !empty(Affiliate::withTrashed()->find($sale->affiliate_id))) {
            $taxaReal -= $affiliateValue;
        }

        if ($sale->status == Sale::STATUS_REFUNDED) {
            $comission = FoxUtils::formatMoney(0);
        }

        //set flag

        if (empty($sale->flag)) {
            $sale->flag = $sale->present()->getPaymentFlag();
        }

        //format dates
        try {
            $sale->hours = (new Carbon($sale->start_date))->format('H:m:s') ?? '';
            $sale->start_date = (new Carbon($sale->start_date))->format('d/m/Y') ?? '';
        } catch (Exception $e) {
            $sale->hours = '';
        }

        if (isset($sale->boleto_due_date)) {
            try {
                $sale->boleto_due_date = (new Carbon($sale->boleto_due_date))->format('d/m/Y');
            } catch (Exception $e) {
                //
            }
        }

        if (!empty($userTransaction->release_date)) {
            $userTransaction->release_date = Carbon::parse($userTransaction->release_date);
        } else {
            $userTransaction->release_date = null;
        }
        $companyName = $userTransaction->company->fantasy_name;

        //add details to sale
        $sale->details = (object)[
            'transaction_rate' => FoxUtils::formatMoney($transactionRate / 100),
            'percentage_rate' => $userTransaction->percentage_rate ?? 0,
            'totalTax' => FoxUtils::formatMoney($totalTax / 100),
            'total' => FoxUtils::formatMoney($total / 100),
            'subTotal' => FoxUtils::formatMoney(intval($subTotal) / 100),
            'discount' => FoxUtils::formatMoney(intval($discount) / 100),
            'automatic_discount' => FoxUtils::formatMoney(intval($sale->automatic_discount) / 100),
            'comission' => $comission,
            'taxa' => FoxUtils::formatMoney($taxa / 100),
            'taxaDiscount' => FoxUtils::formatMoney($totalTaxPercentage / 100),
            'taxaReal' => FoxUtils::formatMoney($taxaReal / 100),
            'release_date' => $userTransaction->release_date != null ? $userTransaction->release_date->format(
                'd/m/Y'
            ) : 'Processando',
            'has_withdrawal' => $userTransaction->withdrawal_id,
            'affiliate_comission' => $affiliateComission,
            'refund_value' => FoxUtils::formatMoney(intval($sale->refund_value) / 100),
            'value_anticipable' => '0,00',
            'total_paid_value' => FoxUtils::formatMoney($sale->total_paid_value),
            'refund_observation' => $sale->saleRefundHistory->count() ? $sale->saleRefundHistory->first()->refund_observation : null,
            'user_changed_observation' => $sale->saleRefundHistory->count() && !$sale->saleRefundHistory->first()->user_id,
            'company_name' => $companyName
        ];
    }

    public function getPagarmeItensList(Sale $sale)
    {
        $itens = [];

        foreach ($sale->plansSales as $key => $planSale) {
            $itens[] = [
                'id' => '#' . Hashids::encode($planSale->plan->id),
                'title' => $planSale->plan->name,
                'unit_price' => str_replace('.', '', $planSale->plan->price),
                'quantity' => $planSale->amount,
                'tangible' => true,
            ];
        }

        return $itens;
    }

    public function getProducts($saleId = null)
    {
        try {
            if ($saleId) {
                $productService = new ProductService();

                $products = $productService->getProductsBySale($saleId);

                return ProductsSaleResource::collection($products);
            } else {
                return null;
            }
        } catch (Exception $ex) {
            Log::warning('Erro ao buscar produtos - SaleService - getProducts');
            report($ex);
        }
    }

    public function getEmailProducts($saleId)
    {
        $saleModel = new Sale();

        $sale = $saleModel->with(
            [
                'productsPlansSale.product'
            ]
        )->find($saleId);
        $productsSale = [];
        if (!empty($sale)) {
            foreach ($sale->productsPlansSale as &$pps) {
                $product = $pps->product->toArray();
                $product['amount'] = $pps->amount;
                if (
                    $product['type_enum'] == (new Product())->present()->getType(
                        'digital'
                    ) && !empty($product['digital_product_url'])
                ) {
                    $product['digital_product_url'] = FoxUtils::getAwsSignedUrl(
                        $product['digital_product_url'],
                        $product['url_expiration_time']
                    );
                    $pps->update(
                        [
                            'temporary_url' => $product['digital_product_url'],
                        ]
                    );
                } else {
                    $product['digital_product_url'] = '';
                }

                $product['photo'] = FoxUtils::checkFileExistUrl(
                    $product['photo']
                ) ? $product['photo'] : 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/produto.png';

                $productsSale[] = $product;
            }
        }

        return $productsSale;
    }

    public function checkPendingDebt($sale, $company, $transactionRefundAmount)
    {
        if(!in_array($sale->gateway_id, [Gateway::GETNET_PRODUCTION_ID, Gateway::GETNET_SANDBOX_ID])) {
            return;
        }

        $getnetBackOffice = new GetnetBackOfficeService();
        $getnetBackOffice->setStatementSubSellerId(CompanyService::getSubsellerId($company))
            ->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'));

        $gatewaySale = $getnetBackOffice->getStatement();

        $gatewaySale = json_decode($gatewaySale);

        if (isset($gatewaySale->list_transactions)) {
            foreach ($gatewaySale->list_transactions as $item) {
                if (
                    isset($item->summary)
                    && isset($item->details)
                    && is_array($item->details)
                    && count($item->details) == 1
                ) {
                    $summary = $item->summary;
                    $details = $item->details[0];

                    $transactionStatusCode = $summary->transaction_status_code;
                    $hasOrderId = empty($summary->order_id) ? false : true;
                    $isTransactionCredit = $details->transaction_sign == '+';

                    $refundObservation = 'Estorno da venda: ' . hashids_encode($sale->id, 'sale_id');

                    if (
                        !is_null($details->subseller_rate_confirm_date) &&
                        $hasOrderId &&
                        $isTransactionCredit &&
                        $transactionStatusCode == GetNetStatementService::TRANSACTION_STATUS_CODE_APROVADO
                    ) {
                        PendingDebt::create(
                            [
                                'company_id' => $company->id,
                                'sale_id' => $sale->id,
                                'type' => PendingDebt::REVERSED,
                                'request_date' => Carbon::now(),
                                'reason' => $refundObservation,
                                'value' => $transactionRefundAmount,
                            ]
                        );
                    }
                }
            }
        }
    }

    public function getSaleTax($sale,$cashbackValue)
    {
        $foxValue = $sale->transactions->whereNull('company_id')->first()->value??0;
        $inviteValue = $sale->transactions->whereNotNull('company_id')->where('type',3)->first()->value??0;

        $saleTax = $foxValue + $cashbackValue + $inviteValue;

        if (!empty(foxutils()->onlyNumbers($sale->interest_total_value))) {
            $saleTax -= foxutils()->onlyNumbers($sale->interest_total_value);
        }

        if (!empty(foxutils()->onlyNumbers($sale->installment_tax_value))) {
            $saleTax -= foxutils()->onlyNumbers($sale->installment_tax_value);
        }

        return $saleTax;
    }

    public function getSaleTaxRefund($sale,$cashbackValue)
    {
        $saleTax = $this->getSaleTax($sale,$cashbackValue);

        if (!empty(foxutils()->onlyNumbers($sale->installment_tax_value))) {
            $saleTax += foxutils()->onlyNumbers($sale->installment_tax_value);
        }

        return $saleTax;
    }

    public function getSaleTotalValue($sale){
        $total = foxutils()->onlyNumbers($sale->sub_total);

        if (!empty(foxutils()->onlyNumbers($sale->shipment_value))) {
            $total += foxutils()->onlyNumbers($sale->shipment_value);
        }

        if (!empty(foxutils()->onlyNumbers($sale->installment_tax_value))) {
            $total -= foxutils()->onlyNumbers($sale->installment_tax_value);
        }

        if (!empty(foxutils()->onlyNumbers($sale->automatic_discount))) {
            $total -= foxutils()->onlyNumbers($sale->automatic_discount);
        }
        return $total;
    }

    public function saleIsGetnet(Sale $sale): bool
    {
        if (in_array($sale->gateway_id, [Gateway::GETNET_SANDBOX_ID, Gateway::GETNET_PRODUCTION_ID])) {
            return true;
        }

        return false;
    }

    public function refund($transactionId, $refundObservation = null)
    {
        try {
            $saleModel = new Sale();
            $transferModel = new Transfer();
            $companyModel = new Company();
            $transactionModel = new Transaction();

            $saleId = current(Hashids::connection('sale_id')->decode($transactionId));

            if (!empty($saleId)) {
                if (getenv('PAGAR_ME_PRODUCTION') == 'true') {
                    $pagarmeClient = new PagarmeClient(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCTION'));
                } else {
                    $pagarmeClient = new PagarmeClient(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
                }

                $sale = $saleModel->find($saleId);
                $refundedTransaction = $pagarmeClient->transactions()->refund(
                    [
                        'id' => $sale->gateway_transaction_id,
                    ]
                );

                $userCompanies = $companyModel->where('user_id', auth()->user()->account_owner_id)->pluck('id');
                $transaction = $transactionModel->where('sale_id', $sale->id)->whereIn('company_id', $userCompanies)
                    ->first();
                $transferModel->create(
                    [
                        'transaction_id' => $transaction->id,
                        'user_id' => auth()->user()->account_owner_id,
                        'value' => 100,
                        'type' => 'out',
                        'type_enum' => $transferModel->present()->getTypeEnum('out'),
                        'reason' => 'Taxa de estorno',
                        'is_refund_tax' => 1,
                        'company_id' => $transaction->company_id,
                    ]
                );
                $transaction->company->update(
                    [
                        'cielo_balance' => $transaction->company->cielo_balance -= 100,
                    ]
                );

                $transferModel->create(
                    [
                        'transaction_id' => $transaction->id,
                        'user_id' => $transaction->company->user_id,
                        'value' => $transaction->value,
                        'type' => 'out',
                        'type_enum' => $transferModel->present()->getTypeEnum('out'),
                        'reason' => 'refunded',
                        'company_id' => $transaction->company->id,
                    ]
                );

                $transaction->company->update(
                    [
                        'cielo_balance' => $transaction->company->cielo_balance -= $transaction->value,
                    ]
                );

                $transaction->update(
                    [
                        'status_enum' => (new Transaction())->present()->getStatusEnum('refunded'),
                        'status' => 'refunded',
                    ]
                );

                $transaction->sale->update(
                    [
                        'gateway_status' => 'refunded',
                        'status' => (new Sale())->present()->getStatus('refunded'),
                    ]
                );
                SaleLog::create(
                    [
                        'sale_id' => $sale->id,
                        'status' => 'refunded',
                        'status_enum' => (new Sale())->present()->getStatus('refunded'),
                    ]
                );

                if (!empty($refundedTransaction)) {
                    SaleRefundHistory::create(
                        [
                            'sale_id' => $sale->id,
                            'refunded_amount' => $sale->original_total_paid_value ?? 0,
                            'date_refunded' => Carbon::now(),
                            'gateway_response' => json_encode($refundedTransaction),
                            'user_id' => auth()->user()->account_owner_id,
                            'refund_observation' => $refundObservation,
                        ]
                    );

                    return
                        [
                            'status' => 'success',
                            'message' => 'Transação estornada com sucesso!',
                        ];
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Erro ao estornar transação',
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Erro ao estornar transação',
                ];
            }
        } catch (Exception $e) {
            Log::warning('Erro ao estornar transação SaleService - refund');
            report($e);
            $message = 'Erro ao estornar transação';
            if ($e->getMessage() == 'Transação já estornada') {
                $message = 'Transação já estornada';
            }

            return [
                'status' => 'error',
                'message' => $message,
            ];
        }
    }

    public function refundBillet(Sale $sale)
    {
        if (in_array($sale->gateway_id, [Gateway::GETNET_SANDBOX_ID, Gateway::GETNET_PRODUCTION_ID])) {
            $this->refundBilletNewFinances($sale);
        } else {
            $this->refundBilletOldFinances($sale);
        }

        $sale->update(
            [
                'status' => $sale->present()->getStatus('billet_refunded'),
                'gateway_status' => 'refunded',
            ]
        );

        SaleLog::create(
            [
                'sale_id' => $sale->id,
                'status' => 'billet_refunded',
                'status_enum' => (new Sale())->present()->getStatus('billet_refunded'),
            ]
        );

        $transactionUser = Transaction::where('sale_id', $sale->id)
            ->where('type', (new Transaction())->present()->getType('producer'))
            ->first();

        //Transferencia de entrada do cliente
        Transfer::create(
            [
                'transaction_id' => $transactionUser->id,
                'user_id' => auth()->user()->account_owner_id,
                'customer_id' => $sale->customer_id,
                'company_id' => $transactionUser->company_id,
                'value' => preg_replace("/[^0-9]/", "", $sale->total_paid_value),
                'type_enum' => (new Transfer())->present()->getTypeEnum('in'),
                'type' => 'in',
                'reason' => 'Estorno de boleto',
            ]
        );

        $sale->customer->update(
            [
                'cielo_balance' => $sale->customer->cielo_balance + preg_replace("/[^0-9]/", "", $sale->total_paid_value),
            ]
        );



        if ( !$sale->api_flag ) {
            event(new BilletRefundedEvent($sale));
        }
    }

    public function refundBilletNewFinances(Sale $sale)
    {
        $transactionModel = new Transaction();

        $cloudfoxTransaction = $sale->transactions()->whereNull('company_id')->first();
        $inviteTransaction = $sale->transactions()->whereNotNull('invitation_id')->first();
        $saleTax = $cloudfoxTransaction->value;
        $getnetService = new GetnetBackOfficeService();

        foreach ($sale->transactions as $transaction) {
            $transaction->update(
                [
                    'status_enum' => (new Transaction())->present()->getStatusEnum('billet_refunded'),
                    'status' => 'billet_refunded',
                ]
            );

            if (empty($transaction->company_id)) {
                continue;
            }

            $refundValue = $transaction->value;

            if ($transaction->type == $transactionModel->present()->getType('producer')) {
                $refundValue += $saleTax;
                if (!empty($inviteTransaction)) {
                    $refundValue += $inviteTransaction->value;
                }
            }

            if (!$transaction->is_waiting_withdrawal && empty($transaction->withdrawal_id)) {
                $transaction->update(
                    [
                        'is_waiting_withdrawal' => true
                    ]
                );
            }

            PendingDebt::create(
                [
                    'company_id' => $transaction->company_id,
                    'sale_id' => $sale->id,
                    'type' => PendingDebt::REVERSED,
                    'request_date' => Carbon::now(),
                    'reason' => 'Estorno do boleto #' . Hashids::connection('sale_id')->encode($sale->id),
                    'value' => $refundValue,
                ]
            );

            if (FoxUtils::isProduction()) {
                $merchantId = env('GET_NET_MERCHANT_ID_PRODUCTION');
                $sellerId = env('GET_NET_SELLER_ID_PRODUCTION');
                $subSellerId = $transaction->company->getGatewaySubsellerId(Gateway::GETNET_PRODUCTION_ID);
            } else {
                $merchantId = env('GET_NET_MERCHANT_ID_SANDBOX');
                $sellerId = env('GET_NET_SELLER_ID_SANDBOX');
                $subSellerId = $transaction->company->getGatewaySubsellerId(Gateway::GETNET_SANDBOX_ID);
            }

            $adjustmentData = [
                'seller_id' => $sellerId,
                'merchant_id' => $merchantId,
                'subseller_id' => $subSellerId,
                'type_adjustment' => 2,
                'amount' => $refundValue,
                'date_adjustment' => today()->addDay()->format('Y-m-d\TH:i:s') . 'Z',
                'description' => 'Estorno do boleto #' . Hashids::connection('sale_id')->encode($sale->id),
            ];

            $response = $getnetService->sendCurl('v1/mgm/adjustment/request-adjustments', 'POST', $adjustmentData);

            $ajdustmentResponse = json_decode($response);

            if (!is_null($ajdustmentResponse->msg_Erro)) {
                report(
                    new Exception(
                        'Erro ao gerar um débito pendente no estorno de boleto da venda ' . $sale->id . ' - ' . $ajdustmentResponse->msg_Erro
                    )
                );
            }
        }
    }

    public function refundBilletOldFinances(Sale $sale)
    {
        $transactionModel = new Transaction();

        $cloudfoxTransaction = $sale->transactions()->whereNull('company_id')->first();
        $inviteTransaction = $sale->transactions()->whereNotNull('invitation_id')->first();
        $saleTax = $cloudfoxTransaction->value;

        foreach ($sale->transactions as $transaction) {
            if (
                $transaction->status_enum == $transactionModel->present()
                    ->getStatusEnum('transfered') && !empty($transaction->company_id)
            ) {
                $refundValue = $transaction->value;

                if ($transaction->type == $transactionModel->present()->getType('producer')) {
                    $refundValue += $saleTax;
                    if (!empty($inviteTransaction)) {
                        $refundValue += $inviteTransaction->value;
                    }
                }

                Transfer::create(
                    [
                        'transaction_id' => $transaction->id,
                        'user_id' => $transaction->company->user_id,
                        'value' => $refundValue,
                        'type' => 'out',
                        'type_enum' => (new Transfer())->present()->getTypeEnum('out'),
                        'reason' => 'Taxa de estorno de boleto',
                        'is_refund_tax' => 1,
                        'company_id' => $transaction->company->id,
                    ]
                );

                $transaction->company->update(
                    [
                        'cielo_balance' => $transaction->company->cielo_balance -= $refundValue,
                    ]
                );
            }

            $transaction->update(
                [
                    'status_enum' => (new Transaction())->present()->getStatusEnum('billet_refunded'),
                    'status' => 'billet_refunded',
                ]
            );
        }
    }

    public function getValuesPartialRefund($sale, $refundValue)
    {
        $totalPaidValue = intval(strval($sale->total_paid_value * 100));
        $totalWithoutInterest = $totalPaidValue - $sale->interest_total_value; // total sem juros
        $newTotalvalue = $totalWithoutInterest - $refundValue; // novo valor total sem juros

        $newTotalValueWithoutInterest = $newTotalvalue;

        $userProject = UserProject::with('company')->where(
            [
                ['type_enum', (new UserProject())->present()->getTypeEnum('producer')],
                ['project_id', $sale->project->id],
            ]
        )->first();

        $user = $userProject->user;
        $company = $userProject->company;

        $installmentFreeTaxValue = 0;
        $interestValue = 0;

        $installmentSelected = $sale->installments_amount;
        $freeInstallments = $sale->project->installments_interest_free;
        $installmentValueTax = intval(($newTotalvalue / 100) * $company->installment_tax);

        if ($installmentSelected == 1) {
            $totalValueWithTax = intval($newTotalvalue);
            $installmentValue = intval($newTotalvalue);
        } else {
            $totalValueWithTax = $newTotalvalue + $installmentValueTax * ($installmentSelected - 1);
            if ($freeInstallments >= $installmentSelected) {
                $installmentValue = intval($newTotalvalue / $installmentSelected);
            } else {
                $installmentValue = intval($totalValueWithTax / $installmentSelected);
            }
        }

        if ($sale->project->installments_interest_free > 1 && $sale->installments_amount <= $sale->project->installments_interest_free) {
            $installmentFreeTaxValue = $totalValueWithTax - $newTotalvalue;
        } else {
            $interestValue = $totalValueWithTax - $newTotalvalue;
            $newTotalvalue = $totalValueWithTax;
        }

        $cloudfoxValue = ((int)(($newTotalvalue - $interestValue) / 100 * $company->gateway_tax));
        $cloudfoxValue += str_replace('.', '', $company->transaction_rate);
        $cloudfoxValue += $interestValue;

        return [
            'cloudfox_value' => $cloudfoxValue,
            'total_value_with_interest' => $newTotalvalue,
            'total_value_without_interest' => $newTotalValueWithoutInterest,
            'installment_free_tax_value' => $installmentFreeTaxValue,
            'interest_value' => $interestValue,
            'value_to_refund' => $totalPaidValue - $newTotalvalue,
        ];
    }

    public function getResumeBlocked($filters)
    {
        $transactionModel = new Transaction();
        $filters['invite'] = 1;
        $transactions = $this->getSalesBlockedBalance($filters);
        $transactionStatus = implode(
            ',',
            [
                $transactionModel->present()->getStatusEnum('transfered'),
                $transactionModel->present()->getStatusEnum('paid'),
            ]
        );

        $resume = $transactions->without(['sale'])
            ->select(
                DB::raw(
                    "
                     sum(CASE WHEN transactions.invitation_id IS NULL THEN 1 ELSE 0 END) as total_sales,
                     sum(CASE WHEN transactions.invitation_id IS NULL THEN
                        if(transactions.status_enum in ({$transactionStatus}), transactions.value, 0) ELSE 0 END
                     ) / 100 as commission,
                     sum(CASE WHEN transactions.invitation_id IS NOT NULL THEN
                        if(transactions.status_enum in ({$transactionModel->present()->getStatusEnum('transfered')}), transactions.value, 0) ELSE 0 END
                     ) / 100 as commission_invite,
                     sum(CASE WHEN transactions.invitation_id IS NULL THEN
                            (sales.sub_total + sales.shipment_value) -
                            (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100
                            ELSE 0 END
                        ) as total"
                )
            )
            ->first()
            ->toArray();

        $resume['commission'] = number_format($resume['commission'], 2, ',', '.');
        $resume['commission_invite'] = number_format($resume['commission_invite'], 2, ',', '.');
        $resume['total'] = number_format($resume['total'], 2, ',', '.');

        return $resume;
    }

    public function getSalesBlockedBalance($filters)
    {
        try {
            $customerModel = new Customer();
            $transactionModel = new Transaction();
            $blockReasonSaleModel = new BlockReasonSale();

            $transactions = $transactionModel->with(
                [
                    'sale.project',
                    'sale.customer',
                    'sale.plansSales.plan',
                    'sale.tracking',
                    'sale.productsPlansSale',
                    'sale.affiliate' => function ($funtionTrash) {
                        $funtionTrash->withTrashed()->with('user');
                    },
                    'blockReasonSale' => function ($blocked) use ($blockReasonSaleModel) {
                        $blocked->where('status', $blockReasonSaleModel->present()->getStatus('blocked'));
                    },
                ]
            )
                ->where('user_id', auth()->user()->account_owner_id)
                ->join('sales', 'sales.id', 'transactions.sale_id')
                ->whereHas(
                    'blockReasonSale',
                    function ($blocked) use ($blockReasonSaleModel) {
                        $blocked->where('status', $blockReasonSaleModel->present()->getStatus('blocked'));
                    }
                );

            if (empty($filters["invite"])) {
                $transactions->whereNull('invitation_id');
            }

            if (!empty($filters["project"])) {

                $projectId = current(Hashids::decode($filters["project"]));
                $transactions->where('sales.project_id', $projectId);
            }

            if (!empty($filters["transaction"])) {
                $saleId = current(
                    Hashids::connection('sale_id')
                        ->decode(str_replace('#', '', $filters["transaction"]))
                );

                $transactions->where('sales.id', $saleId);
            }

            if (!empty($filters["client"])) {
                $customers = $customerModel->where('name', 'LIKE', '%' . $filters["client"] . '%')->pluck('id');
                $transactions->whereIn('sales.customer_id', $customers);
            }

            if (!empty($filters['customer_document'])) {
                $customers = $customerModel->where(
                    'document',
                    FoxUtils::onlyNumbers($filters["customer_document"])
                )->pluck('id');

                if (count($customers) < 1) {
                    $customers = $customerModel->where('document', $filters["customer_document"])->pluck('id');
                }

                $transactions->whereIn('sales.customer_id', $customers);
            }

            if (!empty($filters["payment_method"])) {
                $transactions->where('sales.payment_method', $filters["payment_method"]);
            }

            $status = (!empty($filters['status'])) ? [$filters['status']] : [1, 24];
            if (!empty($filters["plan"])) {
                $planId = current(Hashids::decode($filters["plan"]));
                $transactions->whereHas(
                    'sale.plansSales',
                    function ($query) use ($planId) {
                        $query->where('plan_id', $planId);
                    }
                );
            }

            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

            $transactions->whereBetween(
                'sales.' . $filters["date_type"],
                [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
            )
                ->whereIn('sales.status', $status)
                ->selectRaw('transactions.*, sales.start_date')
                ->orderByDesc('sales.start_date');

            return $transactions;
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    public function getResumePending($filters)
    {
        $transactionModel = new Transaction();
        $transactions = $this->getSalesPendingBalance($filters);
        $transactionStatus = implode(
            ',',
            [
                $transactionModel->present()->getStatusEnum('paid'),
            ]
        );

        $resume = $transactions->without(['sale'])
            ->select(
                DB::raw(
                    "count(sales.id) as total_sales,
                              sum(if(transactions.status_enum in ({$transactionStatus}), transactions.value, 0)) / 100 as commission,
                              sum((sales.sub_total + sales.shipment_value) - (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100) as total"
                )
            )
            ->first()
            ->toArray();

        $resume['commission'] = FoxUtils::formatMoney($resume['commission']);
        $resume['total'] = FoxUtils::formatMoney($resume['total']);

        return $resume;
    }

    public function getSalesPendingBalance($filters)
    {
        $customerModel = new Customer();
        $transactionModel = new Transaction();

        try {
            $relationsArray = [
                'sale',
                'sale.project',
                'sale.customer',
            ];

            $transactions = $transactionModel->with($relationsArray)
                ->where('user_id', auth()->user()->account_owner_id)
                ->join('sales', 'sales.id', 'transactions.sale_id')
                ->where(
                    'transactions.status_enum',
                    '=',
                    $transactionModel->present()->getStatusEnum('paid')
                )
                ->whereNull('invitation_id');

            // Filtro Company
            if (!empty($filters["company"])) {
                $companyId = Hashids::decode($filters["company"]);
                $transactions->where('company_id', $companyId);
            }
/*
            if (!empty($filters['statement']) && $filters['statement'] == 'automatic_liquidation') {
                $transactions->whereIn(
                    'transactions.gateway_id',
                    [Gateway::GETNET_SANDBOX_ID, Gateway::GETNET_PRODUCTION_ID]
                )
                    ->whereNull('transactions.withdrawal_id')
                    ->where('transactions.is_waiting_withdrawal', 0);
            } else {
                $transactions->whereNotIn(
                    'transactions.gateway_id',
                    [Gateway::GETNET_SANDBOX_ID, Gateway::GETNET_PRODUCTION_ID]
                );
            }
*/

            $transactions->whereNull('withdrawal_id');
            if(!empty($filters['acquirer']))
            {
                $gatewayIds = $this->getGatewayIdsByFilter($filters['acquirer']);
                if($filters['acquirer']<> 'Cielo'){
                    $transactions->whereIn('transactions.gateway_id', $gatewayIds);
                }

                switch($filters['acquirer']){
                    case 'Asaas';
                        $transactions->where('transactions.created_at', '>', '2021-09-20');
                    break;
                    case 'Getnet':
                        $transactions->where('is_waiting_withdrawal', 0);
                        break;
                    case 'Cielo':
                        if(auth()->user()->show_old_finances){
                            $transactions->where(function($query) use($gatewayIds) {
                                $query->whereIn('transactions.gateway_id', $gatewayIds)
                                ->orWhere(function($query) {
                                    $query->where('transactions.gateway_id', Gateway::ASAAS_PRODUCTION_ID)->where('transactions.created_at', '<', '2021-09');
                                });
                            });
                        }
                    break;
                }
            }else{
                $transactions->where(function($qr){
                    $qr->where(function($qr2){
                        $qr2->whereIn('transactions.gateway_id', $this->getGatewayIdsByFilter('Asaas'))
                        ->where('transactions.created_at', '>', '2021-09-20');
                    })
                    ->orWhere(function($qr2){
                        $qr2->whereIn('transactions.gateway_id',$this->getGatewayIdsByFilter('Gerencianet'));
                    })
                    ->orWhere(function($qr3){
                        $qr3->where('is_waiting_withdrawal', 0)
                        ->whereIn('transactions.gateway_id',$this->getGatewayIdsByFilter('Getnet'));
                    })
                    ->orWhere(function($qr2){
                        if(auth()->user()->show_old_finances){
                            $qr2->whereIn('transactions.gateway_id', $this->getGatewayIdsByFilter('Cielo'))
                            ->orWhere(function($query) {
                                $query->where('transactions.gateway_id', Gateway::ASAAS_PRODUCTION_ID)->where('transactions.created_at', '<', '2021-09');
                            });
                        }
                    });
                });
            }


            // Filtros - INICIO
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $dateType = $filters["date_type"];

            // Filtro de Data
            $transactions->whereHas(
                'sale',
                function ($querySale) use ($dateRange, $dateType) {
                    $querySale->whereBetween($dateType, [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
                }
            )->selectRaw('transactions.*, sales.start_date')
                ->orderByDesc('sales.start_date');

            // Projeto
            if (!empty($filters["project"])) {
                $projectId = Hashids::decode($filters["project"]);
                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($projectId) {
                        $querySale->where('sales.project_id', $projectId);
                    }
                );
            }

            // Código de Venda
            if (!empty($filters["sale_code"])) {
                $saleId = !empty(Hashids::connection('sale_id')->decode($filters["sale_code"])) ?
                    Hashids::connection('sale_id')->decode($filters["sale_code"]) : 0;

                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($saleId) {
                        $querySale->where('id', $saleId);
                    }
                );
            }

            // Nome do Usuário
            if (!empty($filters["client"])) {
                $customers = $customerModel->where('name', 'LIKE', '%' . $filters["client"] . '%')->pluck('id');
                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($customers) {
                        $querySale->whereIn('customer_id', $customers);
                    }
                );
            }

            // CPF do Usuário
            if (!empty($filters['customer_document'])) {
                $customers = $customerModel->where(
                    'document',
                    FoxUtils::onlyNumbers($filters["customer_document"])
                )->pluck('id');

                if (count($customers) < 1) {
                    $customers = $customerModel->where('document', $filters["customer_document"])->pluck('id');
                }

                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($customers) {
                        $querySale->whereIn('customer_id', $customers);
                    }
                );
            }

            // Forma de pagamento
            if (!empty($filters["payment_method"])) {
                $forma = $filters["payment_method"];
                $transactions->whereHas(
                    'sale',
                    function ($querySale) use ($forma) {
                        $querySale->where('payment_method', $forma);
                    }
                );
            }

            // Reserva de Segurança
            if (!empty($filters['is_security_reserve']) && $filters['is_security_reserve'] == true) {
                $transactions->where('is_security_reserve', true);
            }

            // Filtros - FIM
            return $transactions;
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    public function getPendingBalance($filters)
    {
        $transactions = $this->getSalesPendingBalance($filters);

        return $transactions->paginate(10);
    }

    public function getPaginetedBlocked($filters)
    {
        $transactions = $this->getSalesBlockedBalance($filters);

        return $transactions->paginate(10);
    }

    public function getApprovedSalesInPeriod(User $user, Carbon $startDate, Carbon $endDate)
    {
        return Sale::whereIn(
            'status',
            [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE
            ]
        )
            ->whereBetween(
                'start_date',
                [$startDate->format('Y-m-d') . ' 00:00:00', $endDate->format('Y-m-d') . ' 23:59:59']
            )
            ->where(
                function ($query) use ($user) {
                    $query->where('owner_id', $user->id)
                        ->orWhere('affiliate_id', $user->id);
                }
            );
    }

    public function getCreditCardApprovedSalesInPeriod(User $user, Carbon $startDate, Carbon $endDate)
    {
        $gatewayIds = [Gateway::ASAAS_PRODUCTION_ID, Gateway::GETNET_PRODUCTION_ID];
        if(!FoxUtils::isProduction()){
            $gatewayIds = array_merge($gatewayIds, [Gateway::ASAAS_SANDBOX_ID, Gateway::GETNET_SANDBOX_ID]);
        }
        return Sale::whereIn('gateway_id', $gatewayIds)
            ->where('payment_method', Sale::PAYMENT_TYPE_CREDIT_CARD)
            ->whereIn(
                'status',
                [
                    Sale::STATUS_APPROVED,
                    Sale::STATUS_CHARGEBACK,
                    Sale::STATUS_REFUNDED,
                    Sale::STATUS_IN_DISPUTE
                ]
            )->whereBetween(
                'start_date',
                [$startDate->format('Y-m-d') . ' 00:00:00', $endDate->format('Y-m-d') . ' 23:59:59']
            )->where(
                function ($query) use ($user) {
                    $query->where('owner_id', $user->id)
                        ->orWhere('affiliate_id', $user->id);
                }
            )
            ->get();
    }

    public function returnBlacklistBySale(Sale $sale): array
    {
        try {
            $descriptionBlackList = [];
            if ($sale->status == 10) {
                $saleBlackList = SaleWhiteBlackListResult::where('sale_id', $sale->id)->first();
                if (!empty($saleBlackList)) {
                    if ($saleBlackList->blacklist) {
                        $descriptionBlackListJson = json_decode($saleBlackList->whiteblacklist_json);
                        $descriptionBlackList[] = $descriptionBlackListJson->blackList;
                    }
                }
            }
            return $descriptionBlackList;
        } catch (Exception $e) {
            report($e);
        }
    }

    public function verifyIfUserHasSalesByDate(Carbon $date, int $user_id): bool
    {
        $sale = Sale::where('owner_id', $user_id)
            ->whereDate('start_date', '>=', $date)
            ->where('status', Sale::STATUS_APPROVED)
            ->count();

        return $sale >= 1;
    }

    public static function createSaleLog($saleId, $status)
    {
        try {
            if (is_integer($saleId) && !empty($status)) {
                $statusPresenter = (new Sale)->present()->getStatus($status);
                SaleLog::create(
                    [
                        'sale_id' => $saleId,
                        'status' => is_integer($status) ? $statusPresenter : $status,
                        'status_enum' => is_integer($statusPresenter) ? $statusPresenter : $status,
                    ]
                );
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    public function getGatewayIdsByFilter($nameGateway){
        switch($nameGateway){
            case 'Asaas';
                return [
                    Gateway::ASAAS_PRODUCTION_ID,
                    Gateway::ASAAS_SANDBOX_ID
                ];
            case 'Getnet':
                return [
                    Gateway::GETNET_PRODUCTION_ID,
                    Gateway::GETNET_SANDBOX_ID
                ];
            case 'Gerencianet':
                return [
                    Gateway::GERENCIANET_PRODUCTION_ID,
                    Gateway::GERENCIANET_SANDBOX_ID
                ];
            case 'Cielo':
                return [
                    Gateway::CIELO_PRODUCTION_ID,
                    Gateway::CIELO_SANDBOX_ID
                ];
            break;
        }
        return [];
    }
}
