<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Client;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Products\Transformers\ProductsSaleResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SaleService
 * @package Modules\Core\Services
 */
class SaleService
{
    /**
     * @param $filters
     * @param bool $paginate
     * @return LengthAwarePaginator|Collection
     */
    public function getSales($filters, $paginate = true)
    {
        $companyModel     = new Company();
        $clientModel      = new Client();
        $transactionModel = new Transaction();

        $userCompanies = $companyModel->where('user_id', auth()->user()->id)
                                      ->pluck('id')
                                      ->toArray();

        $transactions = $transactionModel->with([
                                                    'sale',
                                                    'sale.project',
                                                    'sale.client',
                                                    'sale.plansSales',
                                                    'sale.plansSales.plan',
                                                    'sale.plansSales.plan.products',
                                                    'sale.plansSales.plan.project',
                                                    'sale.shipping',
                                                    'sale.checkout',
                                                    'sale.delivery',
                                                ])->whereIn('company_id', $userCompanies)
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

        if (!empty($filters["payment_method"])) {
            $forma = $filters["payment_method"];
            $transactions->whereHas('sale', function($querySale) use ($forma) {
                $querySale->where('payment_method', $forma);
            });
        }

        if (empty($filters['status'])) {
            $status = [1, 2, 4, 6];
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

        if ($paginate) {
            $sales = $transactions->orderBy('id', 'DESC')->paginate(10);
        } else {
            $sales = $transactions->orderBy('id', 'DESC')->get();
        }

        return $sales;
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

        //format dates
        $sale->hours      = (new Carbon($sale->start_date))->format('H:m:s');
        $sale->start_date = (new Carbon($sale->start_date))->format('d/m/Y');
        if (isset($sale->boleto_due_date)) {
            $sale->boleto_due_date = (new Carbon($sale->boleto_due_date))->format('d/m/Y');
        }

        //set flag
        if ((!$sale->flag || empty($sale->flag)) && $sale->payment_method == 1) {
            $sale->flag = 'generico';
        } else if (!$sale->flag || empty($sale->flag)) {
            $sale->flag = 'boleto';
        }

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
        $userCompanies = $companyModel->where('user_id', auth()->user()->id)->pluck('id');

        $transaction = $sale->transactions->whereIn('company_id', $userCompanies)
                                          ->first();

        $transactionConvertax = $sale->transactions
            ->where('company_id', 29)
            ->first();

        if (!empty($transactionConvertax)) {
            $convertaxValue = ($transaction->currency == 'real' ? 'R$ ' : 'US$ ') . substr_replace($transactionConvertax->value, ',', strlen($transactionConvertax->value) - 2, 0);
        } else {
            $convertaxValue = '0,00';
        }

        $value = $transaction->value;

        $comission = ($transaction->currency == 'dolar' ? 'US$ ' : 'R$ ') . substr_replace($value, ',', strlen($value) - 2, 0);

        if ($sale->dolar_quotation != 0) {
            $taxa     = intval($total / $sale->dolar_quotation);
            $taxaReal = 'US$ ' . number_format((intval($taxa - $value)) / 100, 2, ',', '.');
            $iof      =  preg_replace('/[^0-9]/', '', $sale->iof);
            $total    += $iof;
            $sale->iof = number_format($iof / 100, 2, ',', '.');
        } else {
            $taxa     = 0;
            $taxaReal = ($total / 100) * $transaction->percentage_rate + 100;
            $taxaReal = 'R$ ' . number_format($taxaReal / 100, 2, ',', '.');
        }

        //invoices
        $invoices = [];
        foreach ($sale->notazzInvoices as $notazzInvoice) {
            $invoices[] = Hashids::encode($notazzInvoice->id);
        }

        if ($sale->status == 1) {
            $transaction->release_date = Carbon::parse($transaction->release_date);
        } else {
            $transaction->release_date = null;
        }


        //add details to sale
        $sale->details = (object) [
            //invoices
            'invoices'         => $invoices,
            //transaction
            'transaction_rate' => 'R$ ' . number_format(preg_replace('/[^0-9]/', '', $transaction->transaction_rate) / 100, 2, ',', '.'),
            'percentage_rate'  => $transaction->percentage_rate ?? 0,
            //extra info
            'total'            => number_format(intval($total) / 100, 2, ',', '.'),
            'subTotal'         => number_format(intval($subTotal) / 100, 2, ',', '.'),
            'discount'         => number_format(intval($discount) / 100, 2, ',', '.'),
            'comission'        => $comission,
            'convertax_value'  => $convertaxValue,
            'taxa'             => number_format($taxa / 100, 2, ',', '.'),
            'taxaReal'         => $taxaReal,
            'release_date'     => $transaction->release_date != null ? $transaction->release_date->format('d/m/Y') : '',
        ];

        return $sale;
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
     * @param null $saleId
     * @return AnonymousResourceCollection|null
     */
    public function getProductsBySaleId($saleId = null)
    {
        try {
            if ($saleId) {

                $productService = new ProductService();

                $products = $productService->getProductsBySaleId($saleId);

                return ProductsSaleResource::collection($products);
            } else {
                return null;
            }
        } catch (Exception $ex) {
            Log::warning('Erro ao buscar produtos - SaleService - getProducts');
            report($ex);
        }
    }
}
