<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\UserProject;
use Modules\Core\Events\BilletRefundedEvent;
use Modules\Products\Transformers\ProductsSaleResource;
use PagarMe\Client as PagarmeClient;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\Tracking;

/**
 * Class SaleService
 * @package Modules\Core\Services
 */
class SaleService
{
    /**
     * @param $filters
     * @param bool $withProducts
     * @param int $userId
     * @return Builder
     */
    public function getSalesQueryBuilder($filters, $withProducts = false, $userId = 0)
    {
        try {
            $companyModel = new Company();
            $customerModel = new Customer();
            $transactionModel = new Transaction();

            if (!$userId) {
                $userId = auth()->user()->account_owner_id;
            }

            $userCompanies = $companyModel->where('user_id', $userId)
                ->pluck('id')
                ->toArray();

            $relationsArray = [
                'sale',
                'sale.project',
                'sale.customer',
                'sale.plansSales',
                'sale.shipping',
                'sale.checkout',
                'sale.delivery',
                'sale.transactions.anticipatedTransactions',
                'sale.affiliate.user',
                'sale.saleRefundHistory',
            ];

            if ($withProducts) {
                $relationsArray[] = 'sale.productsPlansSale.plan';
                $relationsArray[] = 'sale.productsPlansSale.product';
            }

            $transactions = $transactionModel->with($relationsArray)
                ->whereIn('company_id', $userCompanies)
                ->join('sales', 'sales.id', 'transactions.sale_id')
                ->whereNull('invitation_id');

            if (!empty($filters["project"])) {
                $projectId = current(Hashids::decode($filters["project"]));
                $transactions->whereHas('sale', function ($querySale) use ($projectId) {
                    $querySale->where('project_id', $projectId);
                });
            }

            if (!empty($filters["transaction"])) {
                $saleId = current(Hashids::connection('sale_id')
                    ->decode(str_replace('#', '', $filters["transaction"])));

                $transactions->whereHas('sale', function ($querySale) use ($saleId) {
                    $querySale->where('id', $saleId);
                });
            }

            if (!empty($filters["client"])) {
                $customers = $customerModel->where('name', 'LIKE', '%' . $filters["client"] . '%')->pluck('id');
                $transactions->whereHas('sale', function ($querySale) use ($customers) {
                    $querySale->whereIn('customer_id', $customers);
                });
            }

            if (!empty($filters['customer_document'])) {
                $customers = $customerModel->where('document', FoxUtils::onlyNumbers($filters["customer_document"]))->pluck('id');

                if (count($customers) < 1) {
                    $customers = $customerModel->where('document', $filters["customer_document"])->pluck('id');
                }

                $transactions->whereHas('sale', function ($querySale) use ($customers) {
                    $querySale->whereIn('customer_id', $customers);
                });
            }


            if (!empty($filters['shopify_error']) && $filters['shopify_error'] == true) {
                $transactions->whereHas('sale.project.shopifyIntegrations', function ($queryShopifyIntegration) {
                    $queryShopifyIntegration->where('status', 2);
                });
                $transactions->whereHas('sale', function ($querySaleShopify) {
                    $querySaleShopify->whereNull('shopify_order')->where(
                        'start_date',
                        '<=',
                        Carbon::now()->subMinutes(5)
                            ->toDateTimeString()
                    );
                });
            }
            if (!empty($filters["payment_method"])) {
                $forma = $filters["payment_method"];
                $transactions->whereHas('sale', function ($querySale) use ($forma) {
                    $querySale->where('payment_method', $forma);
                });
            }

            if (empty($filters['status'])) {
                $status = [1, 2, 4, 7, 8, 12, 20, 22, 24];
            } else {
                $status = $filters["status"] == 7 ? [7, 22] : [$filters["status"]];
            }
            if (!empty($filters["plan"])) {
                $planId = current(Hashids::decode($filters["plan"]));
                $transactions->whereHas('sale.plansSales', function ($query) use ($planId) {
                    $query->where('plan_id', $planId);
                });
            }
            if ($filters['status'] == 'chargeback_recovered') {
                $transactions->whereHas('sale', function ($querySale) {
                    $querySale->where('is_chargeback_recovered', true);
                });
            } else {
                $transactions->whereHas('sale', function ($querySale) use ($status) {
                    $querySale->whereIn('status', $status);
                });
            }
            if (!empty($filters['upsell']) && $filters['upsell'] == true) {
                $transactions->whereHas('sale', function ($querySale) {
                    $querySale->whereNotNull('upsell_id');
                });
            }

            //tipo da data e periodo obrigatorio
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $dateType = $filters["date_type"];

            $transactions->whereHas('sale', function ($querySale) use ($dateRange, $dateType) {
                $querySale->whereBetween($dateType, [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
            })->selectRaw('transactions.*, sales.start_date')
                ->orderByDesc('sales.start_date');

            return $transactions;
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * @param $filters
     * @return LengthAwarePaginator
     */
    public function getPaginetedSales($filters)
    {
        $transactions = $this->getSalesQueryBuilder($filters);

        return $transactions->paginate(10);
    }

    /**
     * @param $filters
     * @return array
     * @throws PresenterException
     */
    public function getResume($filters)
    {
        $transactionModel = new Transaction();
        $transactions = $this->getSalesQueryBuilder($filters);
        $transactionStatus = implode(',', [
            $transactionModel->present()->getStatusEnum('paid'),
            $transactionModel->present()
                ->getStatusEnum('transfered'),
            $transactionModel->present()
                ->getStatusEnum('anticipated'),
        ]);
        $statusDispute = (new Sale())->present()->getStatus('in_dispute');

        $resume = $transactions->without(['sale'])
            ->select(DB::raw("count(sales.id) as total_sales,
                              sum(if(transactions.status_enum in ({$transactionStatus}) && sales.status <> {$statusDispute}, transactions.value, 0)) / 100 as commission,
                              sum((sales.sub_total + sales.shipment_value) - (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100) as total"))
            ->first()
            ->toArray();

        $resume['commission'] = number_format($resume['commission'], 2, ',', '.');
        $resume['total'] = number_format($resume['total'], 2, ',', '.');

        return $resume;
    }

    /**
     * @param $saleId
     * @return Sale
     * @throws Exception
     */
    public function getSaleWithDetails($saleId)
    {
        $companyModel = new Company();
        $saleModel = new Sale();

        //get sale
        $sale = $saleModel->with([
            'transactions',
            'notazzInvoices',
            'affiliate',
            'saleRefundHistory',
        ])->find(current(Hashids::connection('sale_id')->decode($saleId)));

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

    /**
     * @param $sale
     * @param $userCompanies
     * @throws Exception
     */
    public function getDetails($sale, $userCompanies)
    {
        $userTransaction = $sale->transactions->where('invitation_id', null)->whereIn('company_id', $userCompanies)
            ->first();

        $anticipatedTransaction = $userTransaction->anticipatedTransactions->first();
        $valueAnticipable = $anticipatedTransaction->value ?? 0;

        //calcule total
        $subTotal = preg_replace("/[^0-9]/", "", $sale->sub_total);

        $total = $subTotal;

        $shipment_value = preg_replace('/[^0-9]/', '', $sale->shipment_value);
        $total += $shipment_value;
        $sale->shipment_value = number_format(intval($shipment_value) / 100, 2, ',', '.');

        if (preg_replace("/[^0-9]/", "", $sale->shopify_discount) > 0) {
            $total -= preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
        } else {
            $discount = '0,00';
        }

        $total -= $sale->automatic_discount;
        $total -= $sale->refund_value;

        //calcule fees
        $transactionConvertax = $sale->transactions
            ->where('company_id', 29)
            ->first();

        if (!empty($transactionConvertax)) {
            $convertaxValue = 'R$ ' . substr_replace($transactionConvertax->value,
                    ',', strlen($transactionConvertax->value) - 2, 0);
        } else {
            $convertaxValue = '0,00';
        }

        //valor do produtor
        $value = $userTransaction->value;

        $comission = 'R$ ' . substr_replace($value, ',', strlen($value) - 2, 0);

        //valor do afiliado
        $affiliateComission = '';
        $affiliateValue = 0;
        if (!empty($sale->affiliate_id)) {
            $affiliate = Affiliate::withTrashed()->find($sale->affiliate_id);
            $affiliateTransaction = $sale->transactions->where('company_id', $affiliate->company_id)->first();
            if (!empty($affiliateTransaction)) {
                $affiliateValue = $affiliateTransaction->value;
                $affiliateComission = ($affiliateTransaction->currency == 'dolar' ? 'US$ ' : 'R$ ') . substr_replace($affiliateValue,
                        ',', strlen($affiliateValue) - 2, 0);
            }
        }

        $taxa = 0;
        $totalToCalcTaxReal = ($sale->present()->getStatus() == 'refunded') ? $total + $sale->refund_value : $total;
        if (preg_replace("/[^0-9]/", "", $sale->installment_tax_value) > 0) {
            $taxaReal = $totalToCalcTaxReal - preg_replace('/[^0-9]/', '', $comission) - preg_replace("/[^0-9]/", "",
                    $sale->installment_tax_value);
        } else {
            $taxaReal = $totalToCalcTaxReal - preg_replace('/[^0-9]/', '', $comission);
        }
        if (!empty($sale->affiliate_id) && !empty(Affiliate::withTrashed()->find($sale->affiliate_id))) {
            $taxaReal -= $affiliateValue;
        }
        $taxaReal = 'R$ ' . number_format($taxaReal / 100, 2, ',', '.');

        //set flag
        if ((!$sale->flag || empty($sale->flag)) && ($sale->payment_method == 1 || $sale->payment_method == 3)) {
            $sale->flag = 'generico';
        } else {
            if (!$sale->flag || empty($sale->flag)) {
                $sale->flag = 'boleto';
            }
        }

        //format dates
        try {
            $sale->hours = (new Carbon($sale->start_date))->format('H:m:s') ?? '';
        } catch (Exception $e) {
            $sale->hours = '';
        }
        try {
            $sale->start_date = (new Carbon($sale->start_date))->format('d/m/Y') ?? '';
        } catch (Exception $e) {
            //
        }
        if (isset($sale->boleto_due_date)) {
            try {
                $sale->boleto_due_date = (new Carbon($sale->boleto_due_date))->format('d/m/Y');
            } catch (Exception $e) {
                //
            }
        }

        if ($sale->status == 1) {
            $userTransaction->release_date = Carbon::parse($userTransaction->release_date);
        } else {
            $userTransaction->release_date = null;
        }
        //add details to sale
        $sale->details = (object)[
            'transaction_rate' => 'R$ ' . number_format(preg_replace('/[^0-9]/', '',
                        $userTransaction->transaction_rate) / 100, 2, ',', '.'),
            'percentage_rate' => $userTransaction->percentage_rate ?? 0,
            'total' => number_format(intval($total) / 100, 2, ',', '.'),
            'subTotal' => number_format(intval($subTotal) / 100, 2, ',', '.'),
            'discount' => number_format(intval($discount) / 100, 2, ',', '.'),
            'automatic_discount' => number_format(intval($sale->automatic_discount) / 100, 2, ',', '.'),
            'comission' => $comission,
            'convertax_value' => $convertaxValue,
            'taxa' => number_format($taxa / 100, 2, ',', '.'),
            'taxaReal' => $taxaReal,
            'release_date' => $userTransaction->release_date != null ? $userTransaction->release_date->format('d/m/Y') : '',
            'affiliate_comission' => $affiliateComission,
            'refund_value' => number_format(intval($sale->refund_value) / 100, 2, ',', '.'),
            'value_anticipable' => number_format(intval($valueAnticipable) / 100, 2, ',', '.'),
            'total_paid_value' => number_format($sale->total_paid_value, 2, ',', '.'),
            'refund_observation' => $sale->saleRefundHistory->count() ? $sale->saleRefundHistory->first()->refund_observation : null,
            'user_changed_observation' => $sale->saleRefundHistory->count() && !$sale->saleRefundHistory->first()->user_id,
        ];
    }

    /**
     * @param Sale $sale
     * @return array
     */
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

    /**
     * @param null $saleId
     * @return AnonymousResourceCollection|null
     */
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

    /**
     * @param $saleId
     * @return array
     */
    public function getEmailProducts($saleId)
    {
        $saleModel = new Sale();

        $sale = $saleModel->with([
            'productsPlansSale.product'
        ])->find($saleId);
        $productsSale = [];
        if (!empty($sale)) {
            foreach ($sale->productsPlansSale as &$pps) {
                $product = $pps->product->toArray();
                $product['amount'] = $pps->amount;
                if($product['type_enum'] == (new Product)->present()->getType('digital') && !empty($product['digital_product_url'])){
                    $product['digital_product_url'] = FoxUtils::getAwsSignedUrl($product['digital_product_url'],$product['url_expiration_time']);
                    $pps->update([
                                     'temporary_url' => $product['digital_product_url'],
                                 ]);
                } else {
                    $product['digital_product_url'] = '';
                }

                $productsSale[] = $product;
            }
        }

        return $productsSale;
    }

    /**
     * @param $sale
     * @param $refundAmount
     * @param $response
     * @param  array  $partialValues
     * @param $refundObservation
     * @return bool
     * @throws Exception
     */
    public function updateSaleRefunded($sale, $refundAmount, $response, $partialValues = [], $refundObservation)
    {
        try {
            $totalPaidValue = preg_replace("/[^0-9]/", "", $sale->total_paid_value);
            DB::beginTransaction();
            $saleModel = new Sale();
            $responseGateway = $response->response ?? [];
            $statusGateway = $response->status_gateway ?? '';

            if (!empty($partialValues)) {
                $status = 'partial_refunded';
                $newTotalPaidValue = $partialValues['total_value_with_interest'];
            } else {
                $status = 'refunded';
                $newTotalPaidValue = $totalPaidValue - $refundAmount;
            }

            $newTotalPaidValue = substr_replace($newTotalPaidValue, '.', strlen($newTotalPaidValue) - 2, 0);
            $updateData = array_filter([
                'total_paid_value' => ($newTotalPaidValue ?? 0),
                'status' => $saleModel->present()->getStatus($status),
                'gateway_status' => $statusGateway,
                'interest_total_value' => $partialValues['interest_value'] ?? null,
                'refund_value' => $sale->refund_value + $refundAmount,
                'installment_tax_value' => $partialValues['installment_free_tax_value'] ?? null,
            ]);

            SaleRefundHistory::create([
                'sale_id' => $sale->id,
                'refunded_amount' => (!empty($partialValues)) ? $partialValues['value_to_refund'] : $refundAmount,
                'date_refunded' => Carbon::now(),
                'gateway_response' => json_encode($responseGateway),
                'refund_value' => $refundAmount,
                'refund_observation' => $refundObservation,
                'user_id' => auth()->user()->account_owner_id,
            ]);
            if ($status == 'refunded') {
                $checktRecalc = $this->recalcSaleRefund($sale, $refundAmount);
            } else {
                if ($status == 'partial_refunded') {
                    $checktRecalc = $this->recalcSaleRefundPartial($sale, $partialValues);
                }
            }
            if ($checktRecalc) {
                $checktUpdate = $sale->update($updateData);
                if ($checktUpdate) {
                    DB::commit();
                    SaleLog::create([
                        'sale_id' => $sale->id,
                        'status' => $status,
                        'status_enum' => $sale->status,
                    ]);
                    try {
                        $shopifyIntegration = ShopifyIntegration::where('project_id', $sale->project_id)->first();
                        if (!FoxUtils::isEmpty($sale->shopify_order) && !FoxUtils::isEmpty($shopifyIntegration)) {
                            $shopifyService = new ShopifyService($shopifyIntegration->url_store,
                                $shopifyIntegration->token, false);

                            $shopifyService->refundOrder($sale);
                            $shopifyService->saveSaleShopifyRequest();
                        }
                    } catch (Exception $ex) {
                        report($ex);
                    }
                }

                return true;
            }
            DB::rollBack();

            return false;
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * @param Sale $sale
     * @param int $refundAmount
     * @return bool
     * @throws Exception
     */
    public function recalcSaleRefund($sale, $refundAmount)
    {
        try {
            $companyModel = new Company();
            $transferModel = new Transfer();
            $transactionModel = new Transaction();

            $totalPaidValue = preg_replace("/[^0-9]/", "", $sale->total_paid_value);
            if ($totalPaidValue > 0) {
                $percentRefund = (int)round((($refundAmount / $totalPaidValue) * 100));
            } else {
                $percentRefund = 100;
            }
            $refundTransactions = $sale->transactions;
            foreach ($refundTransactions as $refundTransaction) {
                //calcula valor que deve ser estornado da transação
                $transactionValue = (int)$refundTransaction->value;
                $transactionRefundAmount = (int)round(($transactionValue * ($percentRefund / 100)));
                //calcula novo valor da transação
                //todo ativar quando for estorno parcial e ver como tratar a comissão ficando zerada
                //                $refundTransaction->value = ($transactionValue - $transactionRefundAmount);
                //Caso transaction ja esteja como transfered, criar transfer de saida
                $company = $companyModel->find($refundTransaction->company_id);
                if ($refundTransaction->status == 'transfered') {
                    $transferModel->create([
                        'transaction_id' => $refundTransaction->id,
                        'user_id' => $company->user_id,
                        'value' => $transactionRefundAmount,
                        'type' => 'out',
                        'type_enum' => $transferModel->present()->getTypeEnum('out'),
                        'reason' => 'refunded',
                        'company_id' => $company->id,
                    ]);

                    $company->update([
                        'balance' => $company->balance -= $transactionRefundAmount,
                    ]);
                } else {
                    if ($refundTransaction->status == 'anticipated') {
                        $company = $companyModel->find($refundTransaction->company_id);

                        $transactionAnticipatedValue = $refundTransaction->value - $refundTransaction->anticipatedTransactions()
                                ->first()->value;

                        $transferModel->create([
                            'transaction_id' => $refundTransaction->id,
                            'user_id' => $company->user_id,
                            'value' => $transactionAnticipatedValue,
                            'type' => 'out',
                            'type_enum' => $transferModel->present()->getTypeEnum('out'),
                            'reason' => 'refunded',
                            'company_id' => $company->id,
                        ]);

                        $company->update([
                            'balance' => $company->balance -= $transactionAnticipatedValue,
                        ]);
                    }
                }
                if ($transactionRefundAmount == $transactionValue) {
                    $refundTransaction->status = 'refunded';
                    $refundTransaction->status_enum = $transactionModel->present()->getStatusEnum('refunded');
                }
                $refundTransaction->save();
            }

            return true;
        } catch
        (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param $transactionId
     * @param null $refundObservation
     * @return string[]
     */
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
                $refundedTransaction = $pagarmeClient->transactions()->refund([
                    'id' => $sale->gateway_transaction_id,
                ]);

                $userCompanies = $companyModel->where('user_id', auth()->user()->account_owner_id)->pluck('id');
                $transaction = $transactionModel->where('sale_id', $sale->id)->whereIn('company_id', $userCompanies)
                    ->first();
                $transferModel->create([
                    'transaction_id' => $transaction->id,
                    'user_id' => auth()->user()->account_owner_id,
                    'value' => 100,
                    'type' => 'out',
                    'type_enum' => $transferModel->present()->getTypeEnum('out'),
                    'reason' => 'Taxa de estorno',
                    'is_refund_tax' => 1,
                    'company_id' => $transaction->company_id,
                ]);
                $transaction->company->update([
                    'balance' => $transaction->company->balance -= 100,
                ]);

                $transferModel->create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->company->user_id,
                    'value' => $transaction->value,
                    'type' => 'out',
                    'type_enum' => $transferModel->present()->getTypeEnum('out'),
                    'reason' => 'refunded',
                    'company_id' => $transaction->company->id,
                ]);

                $transaction->company->update([
                    'balance' => $transaction->company->balance -= $transaction->value,
                ]);

                $transaction->update([
                    'status_enum' => (new Transaction())->present()->getStatusEnum('refunded'),
                    'status' => 'refunded',
                ]);

                $transaction->sale->update([
                    'gateway_status' => 'refunded',
                    'status' => (new Sale())->present()->getStatus('refunded'),
                ]);
                SaleLog::create([
                    'sale_id' => $sale->id,
                    'status' => 'refunded',
                    'status_enum' => (new Sale())->present()->getStatus('refunded'),
                ]);

                if (!empty($refundedTransaction)) {
                    SaleRefundHistory::create([
                        'sale_id' => $sale->id,
                        'refunded_amount' => $sale->original_total_paid_value ?? 0,
                        'date_refunded' => Carbon::now(),
                        'gateway_response' => json_encode($refundedTransaction),
                        'user_id' => auth()->user()->account_owner_id,
                        'refund_observation' => $refundObservation,
                    ]);

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

    /**
     * @param  Sale  $sale
     * @throws PresenterException
     */
    public function refundBillet(Sale $sale)
    {
        $transferModel = new Transfer();
        $transactionModel = new Transaction();

        $sale->update([
            'status' => $sale->present()->getStatus('billet_refunded'),
            'gateway_status' => 'refunded',
        ]);

        $cloudfoxTransaction = $sale->transactions()->whereNull('company_id')->first();
        $inviteTransaction = $sale->transactions()->whereNotNull('invitation_id')->first();
        $saleTax = $cloudfoxTransaction->value;

        foreach ($sale->transactions as $transaction) {
            if ($transaction->status_enum == $transactionModel->present()
                    ->getStatusEnum('transfered') && !empty($transaction->company)) {
                $refundValue = $transaction->value;

                if ($transaction->type == $transactionModel->present()->getType('producer')) {
                    $refundValue += $saleTax;
                    if (!empty($inviteTransaction)) {
                        $refundValue += $inviteTransaction->value;
                    }
                }

                $transferModel->create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->company->user_id,
                    'value' => $refundValue,
                    'type' => 'out',
                    'type_enum' => $transferModel->present()->getTypeEnum('out'),
                    'reason' => 'Estorno',
                    'company_id' => $transaction->company->id,
                ]);

                $transaction->company->update([
                    'balance' => $transaction->company->balance -= $refundValue,
                ]);
            } else {
                if ($transaction->status_enum == $transactionModel->present()
                        ->getStatusEnum('anticipated') && !empty($transaction->company)) {
                    $anticipatedValue = $transaction->anticipatedTransactions()->first()->value;

                    $refundValue = $anticipatedValue;

                    if ($transaction->type == $transactionModel->present()->getType('producer')) {
                        $refundValue += $saleTax;
                        if (!empty($inviteTransaction)) {
                            $refundValue += $inviteTransaction->value;
                        }
                    }

                    $transferModel->create([
                        'transaction_id' => $transaction->id,
                        'user_id' => $transaction->company->user_id,
                        'value' => $refundValue,
                        'type' => 'out',
                        'type_enum' => $transferModel->present()->getTypeEnum('out'),
                        'reason' => 'Estorno',
                        'company_id' => $transaction->company->id,
                    ]);

                    $transaction->company->update([
                        'balance' => $transaction->company->balance -= $refundValue,
                    ]);
                }
            }

            $transaction->update([
                'status_enum' => (new Transaction())->present()->getStatusEnum('billet_refunded'),
                'status' => 'billet_refunded',
            ]);
        }

        $sale->customer->update([
            'balance' => $sale->customer->balance + preg_replace("/[^0-9]/", "", $sale->total_paid_value),
        ]);

        $transactionUser = $transactionModel->where('sale_id', $sale->id)
            ->whereIn('company_id', (new CompanyService())->getCompaniesUser()
                ->pluck('id'))
            ->first();

        //Transferencia de entrada do cliente
        $transferModel->create([
            'transaction_id' => $transactionUser->id,
            'user_id' => auth()->user()->account_owner_id,
            'customer_id' => $sale->customer_id,
            'company_id' => $transactionUser->company_id,
            'value' => preg_replace("/[^0-9]/", "", $sale->total_paid_value),
            'type_enum' => $transferModel->present()->getTypeEnum('in'),
            'type' => 'in',
            'reason' => 'Estorno de boleto',
        ]);

        if (!empty($sale->shopify_order)) {
            try {
                $shopifyIntegration = $sale->project->shopifyIntegrations->first();
                if (!empty($shopifyIntegration)) {
                    $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
                    $shopifyService->cancelOrder($sale);
                }
            } catch (Exception $e) {
            }
        }

        event(new BilletRefundedEvent($sale));
    }

    /**
     * @param $sale
     * @param $partialValues
     * @return bool
     * @throws PresenterException
     */
    public function recalcSaleRefundPartial($sale, $partialValues)
    {
        try {
            $companyModel = new Company();
            $transferModel = new Transfer();

            $refundTransactions = $sale->transactions;

            // criar tranfer de saida e apagar transacoes
            foreach ($refundTransactions as $refundTransaction) {
                $company = $companyModel->find($refundTransaction->company_id);
                if ($refundTransaction->status == 'transfered') {
                    $transferModel->create([
                        'transaction_id' => $refundTransaction->id,
                        'user_id' => $company->user_id,
                        'value' => $refundTransaction->value,
                        'type' => 'out',
                        'type_enum' => $transferModel->present()->getTypeEnum('out'),
                        'reason' => 'refunded',
                        'company_id' => $company->id,
                    ]);

                    $company->update([
                        'balance' => $company->balance -= $refundTransaction->value,
                    ]);
                }
            }

            // recriar transacoes com splitPayment
            $totalValue = $partialValues['total_value_with_interest'];
            $cloudfoxValue = $partialValues['cloudfox_value'];
            $installmentFreeTaxValue = $partialValues['installment_free_tax_value'];

            SplitPaymentPartialRefundService::perform($sale, $totalValue, $cloudfoxValue, $installmentFreeTaxValue,
                $refundTransactions);

            foreach ($refundTransactions as $refundTransaction) {
                $refundTransaction->delete();
            }

            // verify transfers
            $transfersSerice = new TransfersService();
            $transfersSerice->verifyTransactions($sale->id);

            return true;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param $sale
     * @param $refundValue
     * @return array
     * @throws PresenterException
     */
    public function getValuesPartialRefund($sale, $refundValue)
    {
        $totalPaidValue = intval(strval($sale->total_paid_value * 100));
        $totalWithoutInterest = $totalPaidValue - $sale->interest_total_value; // total sem juros
        $newTotalvalue = $totalWithoutInterest - $refundValue; // novo valor total sem juros

        $newTotalValueWithoutInterest = $newTotalvalue;

        $userProject = UserProject::where([
            ['type_enum', (new UserProject())->present()->getTypeEnum('producer')],
            ['project_id', $sale->project->id],
        ])->first();

        $user = $userProject->user;

        $installmentFreeTaxValue = 0;
        $interestValue = 0;

        $installmentSelected = $sale->installments_amount;
        $freeInstallments = $sale->project->installments_interest_free;
        $installmentValueTax = intval(($newTotalvalue / 100) * $user->installment_tax);

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

        $cloudfoxValue = ((int)(($newTotalvalue - $interestValue) / 100 * $user->credit_card_tax));
        $cloudfoxValue += str_replace('.', '', $user->transaction_rate);
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

    /**
     * @param $sale
     */
    public function updateInterestTotalValue($sale)
    {
        $shopifyDiscount = (!is_null($sale->shopify_discount)) ? intval(preg_replace("/[^0-9]/", "",
            $sale->shopify_discount)) : 0;
        $subTotal = intval(strval($sale->sub_total * 100));
        $shipmentValue = intval(strval($sale->shipment_value * 100));
        $automaticDiscount = intval($sale->automatic_discount);
        $totalPaidValue = intval(strval($sale->total_paid_value * 100));
        $interesetTotalValue = $totalPaidValue - (($subTotal + $shipmentValue) - $shopifyDiscount - $automaticDiscount);
        $interesetTotalValue = ($interesetTotalValue < 0) ? 0 : $interesetTotalValue;
        $sale->update(['interest_total_value' => $interesetTotalValue]);
    }

    /**
     * @param $filters
     * @return Builder|null
     */
    public function getSalesPendingBalance($filters)
    {
        $companyModel = new Company();
        $customerModel = new Customer();
        $transactionModel = new Transaction();
        $userId = auth()->user()->account_owner_id;

        try {
            $userCompanies = $companyModel->where('user_id', $userId)
                ->pluck('id')
                ->toArray();

            $relationsArray = [
                'sale',
                'sale.project',
                'sale.customer',
            ];

            $transactions = $transactionModel->with($relationsArray)
                ->whereIn('company_id', $userCompanies)
                ->join('sales', 'sales.id', 'transactions.sale_id')
                ->where('transactions.status_enum' , '=',
                    $transactionModel->present()->getStatusEnum('paid'))
                ->whereNull('invitation_id');


            // Filtros - INICIO
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $dateType = $filters["date_type"];

            // Filtro de Data
            $transactions->whereHas('sale', function ($querySale) use ($dateRange, $dateType) {
                $querySale->whereBetween($dateType, [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
            })->selectRaw('transactions.*, sales.start_date')
                ->orderByDesc('sales.start_date');

            // Projeto
            if (!empty($filters["project"])) {
                $projectId = Hashids::decode($filters["project"]);
                $transactions->whereHas('sale', function ($querySale) use ($projectId) {
                    $querySale->where('sales.project_id', $projectId);
                });
            }

            // Código de Venda
            if (!empty($filters["sale_code"])) {
                $saleId = !empty(Hashids::connection('sale_id')->decode($filters["sale_code"])) ?
                    Hashids::connection('sale_id')->decode($filters["sale_code"]) : 0;

                $transactions->whereHas('sale', function ($querySale) use ($saleId) {
                    $querySale->where('id', $saleId);
                });
            }

            // Nome do Usuário
            if (!empty($filters["client"])) {
                $customers = $customerModel->where('name', 'LIKE', '%' . $filters["client"] . '%')->pluck('id');
                $transactions->whereHas('sale', function ($querySale) use ($customers) {
                    $querySale->whereIn('customer_id', $customers);
                });
            }

            // CPF do Usuário
            if (!empty($filters['customer_document'])) {
                $customers = $customerModel->where('document', FoxUtils::onlyNumbers($filters["customer_document"]))->pluck('id');

                if (count($customers) < 1) {
                    $customers = $customerModel->where('document', $filters["customer_document"])->pluck('id');
                }

                $transactions->whereHas('sale', function ($querySale) use ($customers) {
                    $querySale->whereIn('customer_id', $customers);
                });
            }

            // Forma de pagamento
            if (!empty($filters["payment_method"])) {
                $forma = $filters["payment_method"];
                $transactions->whereHas('sale', function ($querySale) use ($forma) {
                    $querySale->where('payment_method', $forma);
                });
            }
            // Filtros - FIM
            return $transactions;
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * @param $filters
     */
    public function getSalesBlockedBalance($filters)
    {
        try {
            $companyModel     = new Company();
            $customerModel    = new Customer();
            $transactionModel = new Transaction();
            $salesModel       = new Sale();
            $userId           = auth()->user()->account_owner_id;

            $userCompanies = $companyModel->where('user_id', $userId)
                ->pluck('id')
                ->toArray();

            $transactions = $transactionModel->with(
                [
                    'sale.project',
                    'sale.customer',
                    'sale.plansSales.plan',
                    'sale.tracking',
                    'sale.affiliate' => function($funtionTrash) {
                        $funtionTrash->withTrashed()->with('user');
                    }
                ])
                ->whereIn('company_id', $userCompanies)
                ->join('sales', 'sales.id', 'transactions.sale_id')
                ->whereNull('invitation_id')
                ->where('transactions.status_enum', 1)
                ->whereHas('sale', function($f1) use($salesModel){
                    $f1->where('sales.status', $salesModel->present()->getStatus('in_dispute'))
                       ->orWhere(function($f2) use($salesModel) {
                            $f2->where('sales.status', $salesModel->present()->getStatus('approved'))
                               ->whereHas('productsPlansSale', function($q) {
                                    $q->doesntHave('tracking');
                                });
                       })->orWhereHas('tracking', function ($queryTracking) {
                                $presenter = (new Tracking())->present();
                                $queryTracking->where('system_status_enum', $presenter->getSystemStatusEnum('duplicated'));
                        });
                });

            if (!empty($filters["project"])) {
                $projectId = current(Hashids::decode($filters["project"]));
                $transactions->where('sales.project_id', $projectId);
            }

            if (!empty($filters["transaction"])) {
                $saleId = current(Hashids::connection('sale_id')
                    ->decode(str_replace('#', '', $filters["transaction"])));

                $transactions->where('sales.id', $saleId);
            }

            if (!empty($filters["client"])) {
                $customers = $customerModel->where('name', 'LIKE', '%' . $filters["client"] . '%')->pluck('id');
                $transactions->whereIn('sales.customer_id', $customers);
            }

            if (!empty($filters['customer_document'])) {
                $customers = $customerModel->where('document', FoxUtils::onlyNumbers($filters["customer_document"]))->pluck('id');

                if (count($customers) < 1) {
                    $customers = $customerModel->where('document', $filters["customer_document"])->pluck('id');
                }

                $transactions->whereIn('sales.customer_id', $customers);
            }

            if (!empty($filters["payment_method"])) {
                $transactions->where('sales.payment_method', $filters["payment_method"]);
            }

            $status = (!empty($filters['status'])) ? [$filters['status']] : [1,24];
            if (!empty($filters["plan"])) {
                $planId = current(Hashids::decode($filters["plan"]));
                $transactions->whereHas('sale.plansSales', function ($query) use ($planId) {
                    $query->where('plan_id', $planId);
                });
            }

            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

            $transactions->whereBetween('sales.' . $filters["date_type"], [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'])
                         ->whereIn('sales.status', $status)
                         ->selectRaw('transactions.*, sales.start_date')
                         ->orderByDesc('sales.start_date');

            return $transactions;
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * @param $filters
     * @return array
     */
    public function getResumeBlocked($filters)
    {
        $transactionModel = new Transaction();
        $transactions = $this->getSalesBlockedBalance($filters);
        $transactionStatus = implode(',', [
            $transactionModel->present()->getStatusEnum('transfered'),
        ]);

        $resume = $transactions->without(['sale'])
            ->select(DB::raw("count(sales.id) as total_sales,
                              sum(if(transactions.status_enum in ({$transactionStatus}), transactions.value, 0)) / 100 as commission,
                              sum((sales.sub_total + sales.shipment_value) - (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100) as total"))
            ->first()
            ->toArray();

        $resume['commission'] = number_format($resume['commission'], 2, ',', '.');
        $resume['total'] = number_format($resume['total'], 2, ',', '.');

        return $resume;
    }

    public function getResumePending($filters)
    {
        $transactionModel = new Transaction();
        $transactions = $this->getSalesPendingBalance($filters);
        $transactionStatus = implode(',', [
            $transactionModel->present()->getStatusEnum('paid'),
        ]);

        $resume = $transactions->without(['sale'])
            ->select(DB::raw("count(sales.id) as total_sales,
                              sum(if(transactions.status_enum in ({$transactionStatus}), transactions.value, 0)) / 100 as commission,
                              sum((sales.sub_total + sales.shipment_value) - (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100) as total"))
            ->first()
            ->toArray();

        $resume['commission'] = FoxUtils::formatMoney($resume['commission']);
        $resume['total'] = FoxUtils::formatMoney($resume['total']);

        return $resume;
    }

    /**
     * @param $filters
     * @return LengthAwarePaginator
     */
    public function getPendingBalance($filters)
    {
        $transactions = $this->getSalesPendingBalance($filters);

        return $transactions->paginate(10);
    }

    /**
     * @param $filters
     * @return LengthAwarePaginator
     */
    public function getPaginetedBlocked($filters)
    {
        $transactions = $this->getSalesBlockedBalance($filters);

        return $transactions->paginate(10);
    }
}
