<?php

namespace Modules\Sales\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Entities\Plan;
use App\Entities\Sale;
use App\Entities\Client;
use App\Entities\Project;
use App\Entities\Company;
use App\Entities\Product;
use App\Entities\Shipping;
use App\Entities\Checkout;
use App\Entities\Delivery;
use App\Entities\PlanSale;
use Illuminate\Http\Request;
use App\Entities\Transaction;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use Modules\Sales\Transformers\SalesResource;
use Modules\Sales\Exports\Reports\SaleReportExport;

class SalesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $userProjectModel = new UserProject();
            $saleModel        = new Sale();

            $userProjects = $userProjectModel->with('projectId')->where('user', auth()->user()->id)->get();

            $projects = [];

            foreach ($userProjects as $userProject) {
                if ($userProject->projectId != null) {
                    $projects[] = [
                        'id'   => $userProject->projectId->id,
                        'nome' => $userProject->projectId->name,
                    ];
                }
            }

            return view('sales::index', [
                'projetos'     => $projects,
                'sales_amount' => $saleModel->where('owner', auth()->user()->id)->get()->count(),
            ]);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar vendas SalesController - index');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getSaleDetail(Request $request)
    {
        try {
            $requestData = $request->all();

            $saleModel        = new Sale();
            $planSaleModel    = new PlanSale();
            $planModel        = new Plan();
            $clientModel      = new Client();
            $deliveryModel    = new Delivery();
            $checkoutModel    = new Checkout();
            $companyModel     = new Company();
            $transactionModel = new Transaction();
            $planModel        = new Plan();

            if (!empty($requestData['sale_id'])) {
                $sale = $saleModel->with([
                                             'transactions' => function($query) {
                                                 $query->where('company', '!=', null)->first();
                                             },
                                         ])->find(current(Hashids::connection('sale_id')
                                                                 ->decode($requestData['sale_id'])));

                $sale['hours'] = (new Carbon($sale['start_date']))->format('H:m:s');

                $sale['start_date'] = (new Carbon($sale['start_date']))->format('d/m/Y');

                if ($sale->flag) {
                    $sale['flag'] = $sale->flag;
                } else if ((!$sale->flag || empty($sale->flag)) && $sale->payment_method == 1) {
                    $sale['flag'] = 'generico';
                } else {
                    $sale['flag'] = 'boleto';
                }

                $client              = $clientModel->find($sale->client);
                $client['telephone'] = preg_replace("/[^0-9]/", "", $client['telephone']);

                $plansSales = $planSaleModel->with('plan', 'plan.products')->where('sale', $sale->id)
                                            ->get();

                $plans = [];
                $total = 0;

                foreach ($plansSales as $key => $planSale) {
                    $plans[$key]['name']   = $planModel->find($planSale['plan'])->name;
                    $plans[$key]['amount'] = $planSale['amount'];
                    $plans[$key]['value']  = $planSale['plan_value'];
                    $plans[$key]['photo']  = isset($planSale->getRelation('plan')->products[0]) ? $planSale->getRelation('plan')->products[0]->photo : null;
                    $total                 += preg_replace("/[^0-9]/", "", $planSale['plan_value']) * $planSale['amount'];
                }

                $discount = '0,00';
                $subTotal = $total;

                $total += preg_replace("/[^0-9]/", "", $sale->shipment_value);
                if (preg_replace("/[^0-9]/", "", $sale->shopify_discount) > 0) {
                    $total    -= preg_replace("/[^0-9]/", "", $sale->shopify_discount);
                    $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
                } else {
                    $discount = '0,00';
                }

                $delivery             = $deliveryModel->find($sale['delivery']);
                $checkout             = $checkoutModel->find($sale['checkout']);
                $sale->shipment_value = preg_replace('/[^0-9]/', '', $sale->shipment_value);

                $userCompanies = $companyModel->where('user_id', auth()->user()->id)->pluck('id');
                $transaction   = $transactionModel->where('sale', $sale->id)->whereIn('company', $userCompanies)
                                                  ->first();

                if ($transaction) {
                    $value = $transaction->value;
                } else {
                    $value = '000';
                }
                $comission = ($transaction->currency == 'real' ? 'R$ ' : 'US$ ') . substr_replace($value, '.', strlen($value) - 2, 0);

                $taxa     = 0;
                $taxaReal = 0;

                if ($sale->dolar_quotation != 0) {
                    $taxa     = intval($total / $sale->dolar_quotation);
                    $taxaReal = 'US$ ' . number_format((intval($taxa - $value)) / 100, 2, ',', '.');
                    $total    += preg_replace('/[^0-9]/', '', $sale->iof);
                } else {
                    $taxaReal = (intval($total / 100) * $transaction->percentage_rate) + 100;
                    $taxaReal = 'R$ ' . number_format($taxaReal / 100, 2, ',', '.');
                }
                $whatsAppMsg = 'Olá ' . $client->name;
                $details     = view('sales::details', [
                    'sale'           => $sale,
                    'plans'          => $plans,
                    'client'         => $client,
                    'delivery'       => $delivery, 
                    'checkout'       => $checkout,
                    'total'          => number_format(intval($total) / 100, 2, ',', '.'),
                    'subTotal'       => number_format(intval($subTotal) / 100, 2, ',', '.'),
                    'discount'       => number_format(intval($discount) / 100, 2, ',', '.'),
                    'shipment_value' => number_format(intval($sale->shipment_value) / 100, 2, ',', '.'),
                    'whatsapp_link'  => "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $client->telephone) . '&text=' . $whatsAppMsg,
                    'comission'      => $comission,
                    'taxa'           => number_format($taxa / 100, 2, ',', '.'),
                    'taxaReal'       => $taxaReal,
                    'transaction'    => $transaction,
                ]);

                return response()->json($details->render());
            }
        } catch (Exception $e) {
            Log::warning('Erro ao mostrar detalhes da venda  SalesController - details');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refundSale(Request $request)
    {
        return response()->json('sucesso');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getSales(Request $request)
    {
        try {

            $companyModel  = new Company();
            $saleModel     = new Sale();
            $planSaleModel = new PlanSale();
            $planModel     = new Plan();
            $clientModel   = new Client();

            $userCompanies = $companyModel->where('user_id', auth()->user()->id)->pluck('id')->toArray();

            $sales = $saleModel
                ->with([
                           'clientModel', 'plansSales', 'plansSales.plan', 'plansSales.plan.products', 'plansSales.plan.projectId',
                           'transactions' => function($query) use ($userCompanies) {
                               $query->whereIn('company', $userCompanies);
                           },
                       ])
                ->where([['owner', auth()->user()->id], ['status', '!=', 3], ['status', '!=', 10]]);

            if ($request->projeto != '') {
                $plans    = $planModel->where('project', $request->projeto)->pluck('id');
                $salePlan = $planSaleModel->whereIn('plan', $plans)->pluck('sale');
                $sales->whereIn('id', $salePlan);
            }
 
            if ($request->transaction != '') {
                $saleId = current(Hashids::connection('sale_id')->decode(str_replace('#','',$request->transaction)));
                $sales->where('id', $saleId);
            }

            if ($request->comprador != '') {
                $customers = $clientModel->where('name', 'LIKE', '%' . $request->comprador . '%')->pluck('id');
                $sales->whereIn('client', $customers);
            }

            if ($request->forma != '') {
                $sales->where('payment_method', $request->forma);
            }

            if ($request->status != '') {
                $sales->where('status', $request->status);
            }

            if ($request->data_inicial != '' && $request->data_final != '') {
                $sales->whereBetween('start_date', [$request->data_inicial, date('Y-m-d', strtotime($request->data_final . ' + 1 day'))]);
            } else {
                if ($request->data_inicial != '') {
                    $sales->whereDate('start_date', '>=', $request->data_inicial);
                }

                if ($request->data_final != '') {
                    $sales->whereDate('end_date', '<', date('Y-m-d', strtotime($request->data_final . ' + 1 day')));
                }
            }

            return SalesResource::collection($sales->orderBy('id', 'DESC')->paginate(10));
        } catch (Exception $e) {
            Log::warning('Erro ao buscar vendas SalesController - getSales');
            report($e);
        }
    }

    public function getCsvSales(Request $request)
    {

        try {
            $dataRequest = $request->all();
            $dataRequest = array_filter($dataRequest);

            $saleModel     = new Sale();
            $planSaleModel = new PlanSale();
            $clientModel   = new Client();
            $planModel     = new Plan();
            $checkoutModel = new Checkout();
            $shippingModel = new Shipping();

            //$sales = Sale::where('owner',\Auth::user()->id)->orWhere('affiliate',\Auth::user()->id);
            $sales = $this->getSaleModel()
                          ->where('owner', auth()->user()->id);
            //->where('status', '!=', '3');

            if (!empty($dataRequest['select_project'])) {
                $plans    = $planModel->where('project', $dataRequest['select_project'])->pluck('id');
                $salePlan = $planSaleModel->whereIn('plan', $plans)->pluck('sale');
                $sales->whereIn('id', $salePlan);
            }

            if (!empty($dataRequest['client'])) {
                $clientes = $clientModel->where('name', 'LIKE', '%' . $dataRequest['client'] . '%')->pluck('id');
                $sales->whereIn('client', $clientes);
            }

            if (!empty($dataRequest['select_payment_method'])) {
                $sales->where('payment_form', $dataRequest['select_payment_method']);
            }

            if (!empty($dataRequest['sale_status'])) {
                $sales->where('status', $dataRequest['sale_status']);
            }

            if (!empty($dataRequest['start_date'])) {
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dataRequest['start_date'] . ' 00:00:00')
                                       ->toDateTimeString();
                $sales->where('start_date', '>=', $startDateTime ?? null);
            }

            if (!empty($dataRequest['end_date'])) {
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dataRequest['end_date'] . " 23:59:59")
                                     ->toDateTimeString();
                $sales->where('start_date', '<=', $endDateTime ?? null);
            }

            $sales->with(['clientModel', 'projectModel', 'plansSales', 'user', 'affiliate', 'delivery'])//'shippingModel', , 'checkoutModel'
                  ->orderBy('id', 'DESC');

            $salesResult = $sales->get();

            $header = [
                'Projeto',
                'Código da Venda',
                'Dono do Projeto',
                'Afiliado',
                'Forma de Pagamento',
                'Número de Parcelas',
                'Valor da Parcela',
                'Bandeira do Cartão',
                'Link do Boleto',
                'Linha Digitavel do Boleto',
                'Data de Vencimento do Boleto',
                'Data Inicial do Pagamento',
                'Data Final do Pagamento',
                'Data da Criação da  Venda',
                'Status',
                'Gateway Status',
                'Iof',
                'Desconto Shopify',
                'Frete',
                'Valor do Frete',
                'Cotação do dolar',
                'Valor Total Venda',
                'Nome do Cliente',
                'Telefone do Cliente',
                'Email do Cliente',
                'Documento',
                'Endereço',
                'Número',
                'Complemento',
                'Bairro',
                'Cep',
                'Cidade',
                'Estado',
                'País',
                'src',
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_term',
                'utm_content',
                'utm_perfect',

            ];

            $saleData = collect();
            foreach ($salesResult as $sale) {
                $checkout  = $checkoutModel->find($sale->checkout);
                $shipping  = $shippingModel->find($sale->shipping);
                $saleArray = [
                    'project_name'          => $sale->projectModel->name ?? '',
                    'sale_code'             => '#' . strtoupper(Hashids::connection('sale_id')
                                                                       ->encode($sale->id)),
                    'owner'                 => $sale->user->name ?? '',
                    'affiliate'             => null,
                    'payment_form'          => $sale->payment_form ?? '',
                    //'payment_method' => ($sale->payment_method == 1) ? "credit_card" : "boleto",
                    'installments_amount'   => $sale->installments_amount ?? '',
                    'installments_value'    => $sale->installments_value ?? '',
                    'flag'                  => $sale->flag ?? '',
                    'boleto_link'           => $sale->boleto_link ?? '',
                    'boleto_digitable_line' => $sale->boleto_digitable_line ?? '',
                    'boleto_due_date'       => $sale->boleto_due_date ?? '',
                    'start_date'            => $sale->start_date ?? '',
                    'end_date'              => $sale->end_date ?? '',
                    'created_at'            => $sale->created_at ?? '',
                    'status'                => $sale->status ?? '',
                    'gateway_status'        => $sale->gateway_status ?? '',
                    'iof'                   => $sale->iof ?? '',
                    'shopify_discount'      => $sale->shopify_discount ?? '',
                    'shipping'              => $shipping->name ?? '',
                    'shipping_value'        => $shipping->value ?? '',
                    'dolar_quotation'       => $sale->dolar_quotation ?? '',
                    'total_paid'            => $sale->total_paid_value ?? '',
                    'client_name'           => $sale->clientModel->name ?? '',
                    'client_telephone'      => $sale->clientModel->telephone ?? '',
                    'client_email'          => $sale->clientModel->email ?? '',
                    'client_document'       => $sale->clientModel->document ?? '',
                    'client_street'         => $sale->delivery->street ?? '',
                    'client_number'         => $sale->delivery->number ?? '',
                    'client_complement'     => $sale->delivery->complement ?? '',
                    'client_neighborhood'   => $sale->delivery->neighborhood ?? '',
                    'client_zip_code'       => $sale->delivery->zip_code ?? '',
                    'client_city'           => $sale->delivery->city ?? '',
                    'client_state'          => $sale->delivery->state ?? '',
                    'client_country'        => $sale->delivery->country ?? '',
                    'src'                   => $checkout->src ?? '',
                    'utm_source'            => $checkout->utm_source ?? '',
                    'utm_medium'            => $checkout->utm_medium ?? '',
                    'utm_campaign'          => $checkout->utm_campaign ?? '',
                    'utm_term'              => $checkout->utm_term ?? '',
                    'utm_content'           => $checkout->utm_content ?? '',
                ];

                $saleData->push(collect($saleArray));
            }

            //$xlsx = new SaleReportExport($saleData, $header, 16);
            //$z    = Excel::store($xlsx, 'x.xlsx');

            //            return response()->json([
            //                                        'message' => 'Domínio cadastrado com sucesso',
            //                                        'data'    => $xlsx,
            //                                    ], 200);

            //$x=sys_get_temp_dir();

            return Excel::download(new SaleReportExport($saleData, $header, 16), 'export.xlsx');
        } catch (Exception $e) {
            report($e);

            return redirect()->back()->with('error', 'Erro ao tentar gerar o arquivo Excel . ');
        }
    }
}



