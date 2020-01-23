<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Client;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Products\Transformers\ProductsSaleResource;
use PagarMe\Client as PagarmeClient;
use Slince\Shopify\PublicAppCredential;
use Vinkla\Hashids\Facades\Hashids;

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
     * @return Builder|Transaction
     */
    public function getSalesQueryBuilder($filters, $withProducts = false, $userId = 0)
    {
        try {

            $companyModel     = new Company();
            $clientModel      = new Client();
            $transactionModel = new Transaction();

            if (!$userId) {
                $userId = auth()->user()->account_owner_id;
            }

            $userCompanies = $companyModel->where('user_id', $userId)
                                          ->pluck('id')
                                          ->toArray();

            $transactions = $transactionModel->with([
                                                        'sale',
                                                        'sale.project',
                                                        'sale.client',
                                                        'sale.plansSales' . ($withProducts ? '.plan.productsPlans.product' : ''),
                                                        'sale.shipping',
                                                        'sale.checkout',
                                                        'sale.delivery',
                                                        'sale.transactions',
                                                    ])->whereIn('company_id', $userCompanies)
                                                    ->join('sales','sales.id', 'transactions.sale_id')
                                             ->whereNull('invitation_id');

            if (!empty($filters["project"])) {
                $projectId = current(Hashids::decode($filters["project"]));
                $transactions->whereHas('sale', function($querySale) use ($projectId) {
                    $querySale->where('project_id', $projectId);
                });
            }

            if (!empty($filters["transaction"])) {
                $saleId = current(Hashids::connection('sale_id')
                                         ->decode(str_replace('#', '', $filters["transaction"])));

                $transactions->whereHas('sale', function($querySale) use ($saleId) {
                    $querySale->where('id', $saleId);
                });
            }

            if (!empty($filters["client"])) {
                $customers = $clientModel->where('name', 'LIKE', '%' . $filters["client"] . '%')->pluck('id');
                $transactions->whereHas('sale', function($querySale) use ($customers) {
                    $querySale->whereIn('client_id', $customers);
                });
            }
            if (!empty($filters['shopify_error']) && $filters['shopify_error'] == true) {
                $transactions->whereHas('sale.project.shopifyIntegrations', function($queryShopifyIntegration) {
                    $queryShopifyIntegration->where('status', 2);
                });
                $transactions->whereHas('sale', function($querySaleShopify) {
                    $querySaleShopify->whereNull('shopify_order');
                });
            }
            if (!empty($filters["payment_method"])) {
                $forma = $filters["payment_method"];
                $transactions->whereHas('sale', function($querySale) use ($forma) {
                    $querySale->where('payment_method', $forma);
                });
            }

            if (empty($filters['status'])) {
                $status = [1, 2, 4, 6, 7, 20];
            } else {
                $status = [$filters["status"]];
            }

            $transactions->whereHas('sale', function($querySale) use ($status) {
                $querySale->whereIn('status', $status);
            });

            //tipo da data e periodo obrigatorio
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $dateType  = $filters["date_type"];

            $transactions->whereHas('sale', function($querySale) use ($dateRange, $dateType) {
                $querySale->whereBetween($dateType, [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
            });

            return $transactions;
        } catch (Exception $e) {
            report($e);

            return '';
        }
    }

    /**
     * @param $filters
     * @return LengthAwarePaginator
     */
    public function getPaginetedSales($filters)
    {
        $transactions = $this->getSalesQueryBuilder($filters);

        return $transactions->orderBy('sales.start_date', 'DESC')->paginate(10);
    }

    /**
     * @param $filters
     * @return Collection
     */
    public function getAllSales($filters)
    {
        $transactions = $this->getSalesQueryBuilder($filters);

        return $transactions->orderBy('sales.start_date', 'DESC')->get();
    }

    /**
     * @param $saleId
     * @return Sale
     * @throws Exception
     */
    public function getSaleWithDetails($saleId)
    {
        $companyModel = new Company();
        $saleModel    = new Sale();

        //get sale
        $sale = $saleModel->with([
                                     'transactions',
                                     'notazzInvoices',
                                 ])->find(current(Hashids::connection('sale_id')->decode($saleId)));

        //add details to sale
        $userCompanies = $companyModel->where('user_id', auth()->user()->account_owner_id)->pluck('id');
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

        $userTransaction = $sale->transactions->whereIn('company_id', $userCompanies)->first();

        //calcule total
        $subTotal = preg_replace("/[^0-9]/", "", $sale->sub_total);

        $total = $subTotal;

        $shipment_value       = preg_replace('/[^0-9]/', '', $sale->shipment_value);
        $total                += $shipment_value;
        $sale->shipment_value = number_format(intval($shipment_value) / 100, 2, ',', '.');

        if (preg_replace("/[^0-9]/", "", $sale->shopify_discount) > 0) {
            $total    -= preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
        } else {
            $discount = '0,00';
        }

        //calcule fees
        $transactionConvertax = $sale->transactions
            ->where('company_id', 29)
            ->first();

        if (!empty($transactionConvertax)) {
            $convertaxValue = ($userTransaction->currency == 'real' ? 'R$ ' : 'US$ ') . substr_replace($transactionConvertax->value, ',', strlen($transactionConvertax->value) - 2, 0);
        } else {
            $convertaxValue = '0,00';
        }

        $value = $userTransaction->value;

        $comission = ($userTransaction->currency == 'dolar' ? 'US$ ' : 'R$ ') . substr_replace($value, ',', strlen($value) - 2, 0);

        $taxa = 0;
        if (preg_replace("/[^0-9]/", "", $sale->installment_tax_value) > 0) {
            $taxaReal = $total - preg_replace('/[^0-9]/', '', $comission) - preg_replace("/[^0-9]/", "", $sale->installment_tax_value);
        } else {
            $taxaReal = $total - preg_replace('/[^0-9]/', '', $comission);
        }

        $taxaReal = 'R$ ' . number_format($taxaReal / 100, 2, ',', '.');

        //set flag
        if ((!$sale->flag || empty($sale->flag)) && ($sale->payment_method == 1 || $sale->payment_method == 3)) {
            $sale->flag = 'generico';
        } else if (!$sale->flag || empty($sale->flag)) {
            $sale->flag = 'boleto';
        }

        //format dates
        $sale->hours      = (new Carbon($sale->start_date))->format('H:m:s');
        $sale->start_date = (new Carbon($sale->start_date))->format('d/m/Y');
        if (isset($sale->boleto_due_date)) {
            $sale->boleto_due_date = (new Carbon($sale->boleto_due_date))->format('d/m/Y');
        }

        if ($sale->status == 1) {
            $userTransaction->release_date = Carbon::parse($userTransaction->release_date);
        } else {
            $userTransaction->release_date = null;
        }

        //add details to sale
        $sale->details = (object) [
            'transaction_rate' => 'R$ ' . number_format(preg_replace('/[^0-9]/', '', $userTransaction->transaction_rate) / 100, 2, ',', '.'),
            'percentage_rate'  => $userTransaction->percentage_rate ?? 0,
            'total'            => number_format(intval($total) / 100, 2, ',', '.'),
            'subTotal'         => number_format(intval($subTotal) / 100, 2, ',', '.'),
            'discount'         => number_format(intval($discount) / 100, 2, ',', '.'),
            'comission'        => $comission,
            'convertax_value'  => $convertaxValue,
            'taxa'             => number_format($taxa / 100, 2, ',', '.'),
            'taxaReal'         => $taxaReal,
            'release_date'     => $userTransaction->release_date != null ? $userTransaction->release_date->format('d/m/Y') : '',
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
                'id'         => '#' . Hashids::encode($planSale->plan->id),
                'title'      => $planSale->plan->name,
                'unit_price' => str_replace('.', '', $planSale->plan->price),
                'quantity'   => $planSale->amount,
                'tangible'   => true,
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

        $sale         = $saleModel->with(['plansSales.plan.productsPlans.product'])->find($saleId);
        $productsSale = [];
        if (!empty($sale)) {
            /** @var PlanSale $planSale */
            foreach ($sale->plansSales as $planSale) {
                /** @var ProductPlan $productPlan */
                foreach ($planSale->plan->productsPlans as $productPlan) {
                    $product           = $productPlan->product->toArray();
                    $product['amount'] = $productPlan->amount * $planSale->amount;
                    $productsSale[]    = $product;
                }
            }
        }

        return $productsSale;
    }

    /**
     * @param Sale $sale
     * @param int $refundAmount
     * @param $response
     * @return bool
     * @throws Exception
     */
    public function updateSaleRefunded($sale, $refundAmount, $response)
    {
        try {
            $totalPaidValue = preg_replace("/[^0-9]/", "", $sale->total_paid_value);
            DB::beginTransaction();
            $saleModel       = new Sale();
            $responseGateway = $response->response ?? [];
            $statusGateway   = $response->status_gateway ?? '';
            //            'status'         => 'success',
            //            'message'        => 'Venda cancelada com sucesso!',
            //            'status_gateway' => $result['status_gateway'],
            //            'status_sale'    => $result['status'],
            //            'response'       => $result['response'],
            $newTotalPaidValue = $totalPaidValue - $refundAmount;
            if ($newTotalPaidValue <= 0) {
                $status = 'refunded';
            } else {
                $status = 'partial_refunded';
            }
            $newTotalPaidValue = substr_replace($newTotalPaidValue, '.', strlen($newTotalPaidValue) - 2, 0);
            $updateData        = array_filter([
                                                  'total_paid_value' => ($newTotalPaidValue ?? 0),
                                                  'status'           => $saleModel->present()->getStatus($status),
                                                  'gateway_status'   => $statusGateway,
                                              ]);

            SaleRefundHistory::create([
                                          'sale_id'          => $sale->id,
                                          'refunded_amount'  => $refundAmount,
                                          'date_refunded'    => Carbon::now(),
                                          'gateway_response' => json_encode($responseGateway),
                                      ]);
            $checktRecalc = $this->recalcSaleRefund($sale, $refundAmount);
            if ($checktRecalc) {
                $checktUpdate = $sale->update($updateData);
                if ($checktUpdate) {
                    DB::commit();
                    try {
                        $shopifyIntegration = ShopifyIntegration::where('project_id', $sale->project_id)->first();
                        if (!FoxUtils::isEmpty($sale->shopify_order) && !FoxUtils::isEmpty($shopifyIntegration)) {
                            $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

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
            $companyModel   = new Company();
            $transferModel  = new Transfer();
            $totalPaidValue = preg_replace("/[^0-9]/", "", $sale->total_paid_value);
            if ($totalPaidValue > 0) {
                $percentRefund = (int) round((($refundAmount / $totalPaidValue) * 100));
            } else {
                $percentRefund = 100;
            }
            $refundTransactions = $sale->transactions;
            foreach ($refundTransactions as $refundTransaction) {
                //calcula valor que deve ser estornado da transação
                $transactionValue        = (int) $refundTransaction->value;
                $transactionRefundAmount = (int) round(($transactionValue * ($percentRefund / 100)));
                //calcula novo valor da transação
                //todo ativar quando for estorno parcial e ver como tratar a comissão ficando zerada
                //                $refundTransaction->value = ($transactionValue - $transactionRefundAmount);
                //Caso transaction ja esteja como transfered, criar transfer de saida
                $company = $companyModel->find($refundTransaction->company_id);
                if ($refundTransaction->status == 'transfered') {

                    $transferModel->create([
                                               'transaction_id' => $refundTransaction->id,
                                               'user_id'        => $company->user_id,
                                               'value'          => $transactionRefundAmount,
                                               'type'           => 'out',
                                               'reason'         => 'refunded',
                                               'company_id'     => $company->id,
                                           ]);

                    $company->update([
                                         'balance' => $company->balance -= $transactionRefundAmount,
                                     ]);
                } else if ($refundTransaction->status == 'anticipated') {

                    $company = $companyModel->find($refundTransaction->company_id);

                    $transferModel->create([
                                               'transaction_id' => $refundTransaction->id,
                                               'user_id'        => $company->user_id,
                                               'value'          => $refundTransaction->antecipable_value,
                                               'type'           => 'out',
                                               'reason'         => 'refunded',
                                               'company_id'     => $company->id,
                                           ]);

                    $company->update([
                                         'balance' => $company->balance -= $refundTransaction->antecipable_value,
                                     ]);
                }
                if ($transactionRefundAmount == $transactionValue) {
                    $refundTransaction->status = 'refunded';
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
     * @return array
     */
    public function refund($transactionId)
    {
        try {
            $saleModel        = new Sale();
            $transferModel    = new Transfer();
            $companyModel     = new Company();
            $transactionModel = new Transaction();
            $saleId           = current(Hashids::connection('sale_id')->decode($transactionId));
            if (!empty($saleId)) {
                if (getenv('PAGAR_ME_PRODUCTION') == 'true') {
                    $pagarmeClient = new PagarmeClient(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCTION'));
                } else {
                    $pagarmeClient = new PagarmeClient(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
                }

                $sale                = $saleModel->find($saleId);
                $refundedTransaction = $pagarmeClient->transactions()->refund([
                                                                                  'id' => $sale->gateway_transaction_id,
                                                                              ]);

                $userCompanies = $companyModel->where('user_id', auth()->user()->account_owner_id)->pluck('id');
                $transaction   = $transactionModel->where('sale_id', $sale->id)->whereIn('company_id', $userCompanies)
                                                  ->first();
                $transferModel->create([
                                           'transaction_id' => $transaction->id,
                                           'user_id'        => auth()->user()->account_owner_id,
                                           'value'          => 100,
                                           'type'           => 'out',
                                           'reason'         => 'Taxa de estorno',
                                           'company_id'     => $transaction->company_id,
                                       ]);
                $transaction->company->update([
                                                  'balance' => $transaction->company->balance -= 100,
                                              ]);
                sleep(7);
                if (!empty($refundedTransaction)) {
                    SaleRefundHistory::create([
                                                  'sale_id'          => $sale->id,
                                                  'refunded_amount'  => $sale->original_total_paid_value ?? 0,
                                                  'date_refunded'    => Carbon::now(),
                                                  'gateway_response' => json_encode($refundedTransaction),
                                              ]);

                    return
                        [
                            'status'  => 'success',
                            'message' => 'Transação estornada, aguarde alguns instantes para atualizar o status',
                        ];
                } else {
                    return [
                        'status'  => 'error',
                        'message' => 'Erro ao estornar transação',
                    ];
                }
            } else {
                return [
                    'status'  => 'error',
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
                'status'  => 'error',
                'message' => $message,
            ];
        }
    }
}
