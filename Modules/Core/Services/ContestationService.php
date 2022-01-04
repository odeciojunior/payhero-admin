<?php

namespace Modules\Core\Services;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Entities\SaleContestationFile;
use Modules\Sales\Http\Controllers\SalesController;
use PDF;
use Psy\Util\Str;
use stringEncode\Exception;
use Vinkla\Hashids\Facades\Hashids;

class ContestationService
{

    public function getTotalValueContestations($filters)
    {
        $qrConstestations = $this->getQuery($filters);
        $total = $qrConstestations->sum('sales.sub_total');//transactions.value -- sales.sub_total
        $shipmentValue = $qrConstestations->sum('sales.shipment_value');
        $shopifyDiscount = $qrConstestations->sum('sales.shopify_discount');
        $automaticDiscount = $qrConstestations->sum('sales.automatic_discount');

        $total += $shipmentValue;

        if ($shopifyDiscount > 0) {
            $total -= $shopifyDiscount;
        }

        $total -= $automaticDiscount/100;

        return trim(str_replace("R$", "", FoxUtils::formatMoney($total)));// transactions.value/100
    }

    function getQuery($filters)
    {
        $contestations = SaleContestation::select('sale_contestations.*', 'sales.start_date', 'customers.name as customer_name',
        'sales.total_paid_value','sales.sub_total','sales.shipment_value',\DB::Raw("CAST(sales.shopify_discount as DECIMAL) AS shopify_discount"))
            ->selectRaw(\DB::raw("(CASE WHEN expiration_date > '". Carbon::now()->addDay(2)->endOfDay()."' THEN 1 ELSE 0 END) as custom_expired"))
            ->join('sales', 'sales.id', 'sale_contestations.sale_id')
            ->leftJoin('users', 'users.id', '=', 'sales.owner_id')
             ->join('transactions', function ($query) {
                  $query->on('sales.id', '=', 'transactions.sale_id')
                 ->where('transactions.type', '=', 2);
             })
//                        ->join('companies', 'companies.id', '=', 'transactions.company_id')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->where('sales.owner_id', \Auth::user()->account_owner_id);


        //Data da compra = transaction_date, Data do chargeback =  adjustment_date
        $contestations->when(request('date_type'), function ($query, $search) {

            $dateRange = FoxUtils::validateDateRange(request('date_range'));

            $search_input_date = 'sale_contestations.expiration_date';

            if ($search == 'adjustment_date') {
                $search_input_date = 'sale_contestations.file_date';
            }

            if ($search == 'transaction_date') {
                $search_input_date = 'sales.start_date';
            }

            if ($search == 'expiration_date') {
                $search_input_date = 'sale_contestations.expiration_date';
            }

            return $query->whereBetween(
                $search_input_date,
                [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
            );

        });

        $contestations->when(request('transaction'), function ($query, $search) {

            $sale_id = Hashids::connection('sale_id')->decode($search);
            return $query->where('sale_contestations.sale_id', $sale_id[0]);
        });

        $contestations->when(request('contestation_situation'), function ($query, $situation) {
            $situationStatus = [
                '0' => null,
                '1' => SaleContestation::STATUS_IN_PROGRESS,
                '2' => SaleContestation::STATUS_LOST,
                '3' => SaleContestation::STATUS_WIN
            ];

            return $query->where('sale_contestations.status', $situationStatus[$situation]);
        });

        $contestations->when(request('is_contested'), function ($query, $val) {
            if($val == 1){
                return $query->where('sale_contestations.file_user_completed', 1);
            }

            if($val == 2){
                return $query->where('sale_contestations.file_user_completed', 0);
            }

        });

        $contestations->when(request('is_expired'), function ($query, $val) {
            $date = Carbon::now()->addDay(2);
            if($val == 1){
                return $query->whereDate('sale_contestations.expiration_date', '<=', $date);
            }
            if($val == 2){
                return $query->whereDate('sale_contestations.expiration_date', '>', $date);
            }
        });

        $contestations = $contestations->orderBy('custom_expired', 'desc');
        $contestations->when(request('order_by_expiration_date'), function ($query, $search) {
            return $query->orderBy('expiration_date', 'asc');
        });

        $contestations->when(!request('order_by_expiration_date'), function ($query, $search) {
            $data_type = \request('date_type');

            if ($data_type == 'transaction_date')
                return $query->orderBy('sales.start_date', 'desc');

            if ($data_type == 'adjustment_date')
                return $query->orderBy('sale_contestations.file_date', 'desc');

            if ($data_type == 'expiration_date')
                return $query->orderBy('sale_contestations.expiration_date', 'asc');

        });


//        $contestations->when(request('fantasy_name'), function ($query, $search) {
//            return $query->where(
//                'companies.fantasy_name',
//                'like',
//                '%' . $search . '%'
//            );
//        });

        $contestations->when(request('getnet_terminal_nsu'), function ($query, $search) {
            return $query->where('data', 'like', "%" . $search . "%");
        });

        $contestations->when(request('project'), function ($query, $search) {
            $projectId = current(Hashids::decode($search));
            return $query->where('sales.project_id', $projectId);
        });

        $contestations->when(request('customer'), function ($query, $search) {
            return $query->where('customers.name', 'like', '%' . $search . '%');
        });

        $contestations->when(request('customer_document'), function ($query, $search) {

            return $query->where(
                'customers.document',
                'like',
                '%' . $search . '%'
            );
        });
        $contestations->when(request('sale_approve'), function ($query, $val) {
            if($val==1){
                return $query->where('sales.status', Sale::STATUS_APPROVED);
            }
        });

        return $contestations;

    }

    public function getTotalContestations($filters)
    {

        $getnetChargebacks = $this->getQuery($filters);
        return $getnetChargebacks->count();
    }

    public function getTotalWonContestations($filters)
    {
        $wonContestations = $this->getQuery($filters)->where('sale_contestations.status', SaleContestation::STATUS_WIN);
        return $wonContestations->count();
    }

    public function getWonContestationsTax($totalContestations, $totalWonContestations)
    {
        if ($totalWonContestations == 0)
            return '0,00%';

        $totalContestationsTax = $totalWonContestations > 0 ? number_format(($totalWonContestations * 100) / $totalContestations, 2, ',', '.') . '%' : '0,00%';
        return $totalContestationsTax;
    }

    public function getTotalApprovedSales($filters)
    {
        $dateRange = FoxUtils::validateDateRange($filters["date_range"]);

        $totalSaleApproved = Sale::where('gateway_id', 15)
            ->where('payment_method', 1)
            ->whereIn('status', [1, 4, 7, 24])
            ->where('sales.owner_id', \Auth::user()->account_owner_id);

//        if ($filters['date_type'] == 'transaction_date') {
//            $totalSaleApproved->whereBetween(
//                'sales.start_date',
//                [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
//            );
//        }else if ($filters['date_type'] == 'expiration_date') {
//
//            $totalSaleApproved->whereHas('contestations', function ($query) use ($dateRange) {
//                $query->whereBetween(
//                    'sale_contestations.expiration_date',
//                    [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
//                );
//            });
//
//        }else {
//
//            $totalSaleApproved->whereHas('contestations', function ($query) use ($dateRange) {
//                $query->whereBetween(
//                    'sale_contestations.request_date',
//                    [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
//                );
//            });
//        }

        $totalSaleApproved->whereBetween(
            'sales.start_date',
            [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']
        );

        if (!empty($filters['transaction'])) {
            preg_match_all('/[0-9A-Za-z]+/', $filters['transaction'], $matches);
            $transactions = array_map(
                function ($item) {
                    return is_numeric($item)
                        ? $item
                        : current(Hashids::connection('sale_id')->decode($item));
                },
                current($matches)
            );
            $totalSaleApproved->whereIn('id', $transactions);
        }

//        if (!empty($filters['fantasy_name'])) {
//            $totalSaleApproved->whereHas(
//                'user.companies',
//                function ($query) use ($filters) {
//                    $query->where(
//                        'fantasy_name',
//                        'like',
//                        '%' . $filters['fantasy_name'] . '%'
//                    );
//                }
//            );
//        }

        if (!empty($filters['project'])) {
            $projectId = current(Hashids::decode($filters['project']));
            $totalSaleApproved->where('project_id', $projectId);
        }

        if (!empty($filters['user'])) {
            $userId = current(Hashids::decode($filters['user']));
            $totalSaleApproved->where('owner_id', $userId);
        }

        if (!empty($filters['customer'])) {
            $totalSaleApproved->where('customer_id', $filters['customer']);

        }

        if (!empty($filters['customer_document'])) {
            $document = $filters['customer_document'];
            $totalSaleApproved->whereHas(
                'customer',
                function ($query) use ($document) {
                    $query->where('document', $document);
                }
            );
        }

        return $totalSaleApproved->count();
    }

    public function getChargebackTax($totalChargebacks, $totalApprovedSales)
    {
        if ($totalApprovedSales == 0)
            return '0,00%';

        $totalChargebackTax = $totalChargebacks > 0 ? number_format(($totalChargebacks * 100) / $totalApprovedSales, 2, ',', '.') . '%' : '0,00%';
        return $totalChargebackTax;
    }

    public function generateDispute(SaleContestation $contestation)
    {
        $productService = new ProductService;

        $sale = Sale::with([
            'transactions',
            'customer',
            'delivery',
            'trackings',
        ])->find($contestation->sale_id);

        if (empty($sale->id)) {
            throw new Exception('Venda não encontrada');
        }

        $ip = geoip()->getLocation($sale->checkout->ip);

        $products = $productService->getProductsBySale($sale);
        $products_str = '';
        foreach ($products as $product) {
            $products_str .= $product->name . ', ';
        }

        $affiliateComission = '';

        if (!empty($sale->affiliate_id)) {
            $affiliate = Affiliate::withTrashed()->find($sale->affiliate_id);

            $affiliateTransaction = $sale->transactions->where('company_id', $affiliate->company_id)->first();
            if (!empty($affiliateTransaction)) {
                $affiliateValue = $affiliateTransaction->value;
                $affiliateComission = ($affiliateTransaction->currency == 'dolar' ? 'US$ ' : 'R$ ') . substr_replace($affiliateValue, ',', strlen($affiliateValue) - 2, 0);
            }
            $affiliateName = $affiliate->user->name;
        }

        $saleTrackings = $sale->trackings->pluck('tracking_code')->toArray();
        $saleTrackings = array_unique($saleTrackings);

        $trackings = [];
        foreach ($saleTrackings as $saleTracking) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://tracking.cloudfox.net/api/tracking/detail/' . $saleTracking);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $return = json_decode(curl_exec($ch));
            curl_close($ch);
            if (!empty($return->data)) {
                $trackings[] = $return->data;
            }
        }

        $request = new Request();
        $encoded_saleid = Hashids::connection('sale_id')->encode($sale->id);
        $request->merge(["sale_id" => $encoded_saleid]);
        $sale_details = (new SalesController())->getSaleDetail($request, true);

        $dataSale = (object)[
            'id' => $encoded_saleid,
            'data_decoded' => json_decode($contestation->data, true),
            'transaction_date' => \Carbon\Carbon::parse($contestation->transaction_date)->format('d/m/Y'),
            'products_str' => $products_str,
            'tranckings' => $trackings,
            'nsu' => $contestation->nsu,
            'payment_method' => $sale->payment_method,
            'flag' => $sale->flag,
            'start_date' => $sale->start_date,
            'hours' => $sale->hours,
            'status' => $sale->status,
            'installments_amount' => $sale->installments_amount,
            'boleto_link' => $sale->boleto_link,
            'boleto_digitable_line' => $sale->boleto_digitable_line,
            'boleto_due_date' => $sale->boleto_due_date,
            'attempts' => $sale->attempts,
            'shipment_value' => $sale->shipment_value,
            'transaction_rate' => $sale->details->transaction_rate ?? null,
            'percentage_rate' => $sale->details->percentage_rate ?? null,
            'total' => $sale->details->total ?? null,
            'subTotal' => $sale->details->subTotal ?? null,
            'discount' => $sale->details->discount ?? null,
            'comission' => $sale->details->comission ?? null,
            'convertax_value' => $sale->details->convertax_value ?? null,
            'taxa' => $sale->details->taxa ?? null,
            'taxaReal' => $sale->details->taxaReal ?? null,
            'installment_tax_value' => $sale->present()->getInstallmentValue,
            'release_date' => isset($sale->details->release_date) ? $sale->details->release_date : null,
            'affiliate_comission' => $affiliateComission,
            'shopify_order' => $sale->shopify_order ?? null,
            'automatic_discount' => $sale->details->automatic_discount ?? 0,
            'refund_value' => $sale->details->refund_value ?? '0,00',
            'value_anticipable' => $sale->details->value_anticipable ?? null,
            'total_paid_value' => $sale->total_paid_value,
            'customer' => (object)$sale->customer->getAttributes(),
            'delivery' => (object)$sale->delivery->getAttributes(),
            'affiliate' => $affiliateName ?? null,
            'ip' => (object)$ip,
            'operational_system' => $sale->checkout->operational_system,
            'browser' => $sale->checkout->browser,
        ];

        $pages = ['resume', 'user', 'sale', 'payment'];
        foreach ($trackings as $tracking) {
            $pages[] = 'tracking';
        }
        $numero_atividade = $dataSale->data_decoded['Número de Atividade'];
        $numero_referencia = $dataSale->data_decoded['Número de Referência'];

        $pdf_name = $numero_referencia . '_' . $numero_atividade . '.pdf';
        $tracking = array_shift($trackings);

        $pdf = PDF::loadView('chargebacks::contestchargeback', compact('dataSale', 'contestation', 'products', 'tracking', 'sale_details'));

        return $this->uploadPdfToS3($pdf, $pdf_name);

    }

    private function uploadPdfToS3($pdf, $pdf_name)
    {
        $path = 'uploads/private/contestations/' . $pdf_name;

        Storage::disk('s3_documents')->put(
            $path,
            $pdf->output(),
            'private'
        );

        $path = Storage::disk('s3_documents')->temporaryUrl($path, Carbon::now()
            ->addSeconds(60));

        return [
            'file_path' => $path,
            'file_name' => $pdf_name
        ];

    }

    public function sendContestationFiles($files)
    {

        $paths = [];
        $amazonFileService = app(AmazonFileService::class);
        $amazonFileService->setDisk('s3_documents');
        foreach ($files['files'] as $file) {

            $image = $file->getClientOriginalName();
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $image_name = uniqid() . time() . '.' . $extension;
            $path = 'uploads/private/contestations';

            $pathamz = $amazonFileService->uploadFile(
                $path,
                $file,
                $image_name,
                null,
                'private'
            );

            $paths[] = $amazonFileService->getPath($pathamz);

        }

        return $paths;


    }

    public function removeFile(SaleContestationFile $saleContestationFile )
    {

        try {

            $sDrive = Storage::disk('s3_documents');
            if($sDrive->exists($saleContestationFile->file)){
                $sDrive->delete($saleContestationFile->file);
                $saleContestationFile->delete();
            }

        } catch (Exception $e) {
            Log::warning('DeleteTemporaryFiles - Error command ');
            report($e);
        }

    }


}
