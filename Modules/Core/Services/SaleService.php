<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Client;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
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
        $companyModel = new Company();
        $clientModel = new Client();
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
            'sale.delivery'
        ])->whereIn('company_id', $userCompanies)
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
            $customers = $clientModel->where('name', 'LIKE', '%' . $filters["client"] . '%')->pluck('id');
            $transactions->whereHas('sale', function ($querySale) use ($customers) {
                $querySale->whereIn('client_id', $customers);
            });
        }

        if (!empty($filters["payment_method"])) {
            $forma = $filters["payment_method"];
            $transactions->whereHas('sale', function ($querySale) use ($forma) {
                $querySale->where('payment_method', $forma);
            });
        }

        if (empty($filters['status'])) {
            $status = [1, 2, 4, 6];
        } else {
            $status = [$filters["status"]];
        }

        $transactions->whereHas('sale', function ($querySale) use ($status) {
            $querySale->whereIn('status', $status);
        });

        //tipo da data e periodo obrigatorio
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
        $dateType = $filters["date_type"];

        $transactions->whereHas('sale', function ($querySale) use ($dateRange, $dateType) {
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
        $transactionModel = new Transaction();
        $saleModel = new Sale();

        $sale = $saleModel->with([
            'transactions' => function ($query) {
                $query->where('company_id', '!=', null)->first();
            },
            'notazzInvoices',
            'plansSales'
        ])->find(current(Hashids::connection('sale_id')->decode($saleId)));

        $sale->hours = (new Carbon($sale->start_date))->format('H:m:s');
        $sale->start_date = (new Carbon($sale->start_date))->format('d/m/Y');

        if (isset($sale->boleto_due_date)) {
            $sale->boleto_due_date = (new Carbon($sale->boleto_due_date))->format('d/m/Y');
        }

        if ((!$sale->flag || empty($sale->flag)) && $sale->payment_method == 1) {
            $sale->flag = 'generico';
        } elseif (!$sale->flag || empty($sale->flag)) {
            $sale->flag = 'boleto';
        }

        $discount = '0,00';
        $subTotal = $this->getSubTotal($sale);
        $total = $subTotal;


        $shipment_value = preg_replace('/[^0-9]/', '', $sale->shipment_value);
        $total += $shipment_value;
        if (preg_replace("/[^0-9]/", "", $sale->shopify_discount) > 0) {
            $total -= preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
        } else {
            $discount = '0,00';
        }
        $sale->shipment_value = number_format(intval($shipment_value) / 100, 2, ',', '.');

        $userCompanies = $companyModel->where('user_id', auth()->user()->id)->pluck('id');
        $transaction = $transactionModel->where('sale_id', $sale->id)->whereIn('company_id', $userCompanies)
            ->first();

        $transactionConvertax = $transactionModel->where('sale_id', $sale->id)
            ->where('company_id', 29)
            ->first();

        if (!empty($transactionConvertax)) {
            $convertaxValue = ($transaction->currency == 'real' ? 'R$ ' : 'US$ ') . substr_replace($transactionConvertax->value, ',', strlen($transactionConvertax->value) - 2, 0);
        } else {
            $convertaxValue = '0,00';
        }

        $value = $transaction->value;

        $comission = ($transaction->currency == 'real' ? 'R$ ' : 'US$ ') . substr_replace($value, ',', strlen($value) - 2, 0);

        $taxa = 0;
        $taxaReal = 0;

        if ($sale->dolar_quotation != 0) {
            $taxa = intval($total / $sale->dolar_quotation);
            $taxaReal = 'US$ ' . number_format((intval($taxa - $value)) / 100, 2, ',', '.');
            $total += preg_replace('/[^0-9]/', '', $sale->iof);
        } else {
            $taxaReal = ($total / 100) * $transaction->percentage_rate + 100;
            $taxaReal = 'R$ ' . number_format($taxaReal / 100, 2, ',', '.');
        }

        $invoices = [];
        foreach ($sale->notazzInvoices as $notazzInvoice) {
            $invoices[] = Hashids::encode($notazzInvoice->id);
        }

        $sale->details = (object)[
            //invoices
            'invoices'              => $invoices,
            //transaction
            'transaction_rate'      => $transaction->transaction_rate,
            'percentage_rate'       => $transaction->percentage_rate,
            //extra info
            'total'                 => number_format(intval($total) / 100, 2, ',', '.'),
            'subTotal'              => number_format(intval($subTotal) / 100, 2, ',', '.'),
            'discount'              => number_format(intval($discount) / 100, 2, ',', '.'),
            'comission'             => $comission,
            'convertax_value'       => $convertaxValue,
            'taxa'                  => number_format($taxa / 100, 2, ',', '.'),
            'taxaReal'              => $taxaReal,
        ];

        return $sale;
    }


    /**
     * @param Sale $sale
     * @return float|int
     */
    public function getSubTotal(Sale $sale)
    {
        $subTotal = 0;
        foreach ($sale->plansSales as $planSale) {
            $subTotal += preg_replace("/[^0-9]/", "", $planSale->plan()->first()->price) * $planSale->amount;
        }

        return $subTotal;
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
     * @return array
     */
    public function getProducts($saleId = null)
    {
        try {
            $saleModel = new Sale();
            $productModel = new Product();

            if ($saleId) {

                $sale = $saleModel->with([
                    'plansSales.plan.products',
                ])->find($saleId);

                $plansIds = $sale->plansSales->pluck('plan_id')->toArray();

                $products = $productModel->whereHas('productsPlans', function ($query) use ($plansIds) {
                    $query->whereIn('plan_id', $plansIds);
                })->get();

                return $products;
            } else {
                return null;
            }
        } catch (Exception $ex) {
            Log::warning('Erro ao buscar produtos - SaleService - getProducts');
            report($ex);
        }
    }
}
