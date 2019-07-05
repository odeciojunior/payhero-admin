<?php

namespace Modules\Sales\Http\Controllers;

use App\Entities\Company;
use App\Entities\Product;
use App\Entities\Shipping;
use App\Entities\Transaction;
use Carbon\Carbon;
use App\Entities\Plan;
use App\Entities\Sale;
use App\Entities\Client;
use App\Entities\Project;
use App\Entities\Checkout;
use App\Entities\Delivery;
use App\Entities\PlanSale;
use Exception;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Sales\Exports\Reports\SaleReportExport;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use Modules\Sales\Transformers\SalesResource;

class SalesController extends Controller
{
    /**
     * @var Sale
     */
    private $saleModel;
    /**
     * @var PlanSale
     */
    private $plansSalesModel;
    /**
     * @var Client
     */
    private $clientModel;
    /**
     * @var Delivery
     */
    private $deliveryModel;
    /**
     * @var Checkout
     */
    private $checkoutModel;
    /**
     * @var Plan
     */
    private $planModel;
    /**
     * @var Product
     */
    private $productModel;
    /**
     * @var UserProject
     */
    private $userProjectModel;
    /**
     * @var Company
     */
    private $companyModel;
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getSaleModel()
    {
        if (!$this->saleModel) {
            $this->saleModel = app(Sale::class);
        }

        return $this->saleModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getPlansSales()
    {
        if (!$this->plansSalesModel) {
            $this->plansSalesModel = app(PlanSale::class);
        }

        return $this->plansSalesModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getClient()
    {
        if (!$this->clientModel) {
            $this->clientModel = app(Client::class);
        }

        return $this->clientModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDelivery()
    {
        if (!$this->deliveryModel) {
            $this->deliveryModel = app(Delivery::class);
        }

        return $this->deliveryModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCheckout()
    {
        if (!$this->checkoutModel) {
            $this->checkoutModel = app(Checkout::class);
        }

        return $this->checkoutModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getPlan()
    {
        if (!$this->planModel) {
            $this->planModel = app(Plan::class);
        }

        return $this->planModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getProduct()
    {
        if (!$this->productModel) {
            $this->productModel = app(Product::class);
        }

        return $this->productModel;
    }

    /**
     * @return Company|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCompany()
    {
        if (!$this->companyModel) {
            $this->companyModel = app(Company::class);
        }

        return $this->companyModel;
    }

    /**
     * @return Transaction|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getTransaction()
    {
        if (!$this->transaction) {
            $this->transaction = app(Transaction::class);
        }

        return $this->transaction;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUserProjectModel()
    {
        if (!$this->userProjectModel) {
            $this->userProjectModel = app(UserProject::class);
        }

        return $this->userProjectModel;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $userProjects = $this->getUserProjectModel()->with('projectId')->where('user', auth()->user()->id)->get();

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
                'sales_amount' => $this->getSaleModel()->where('owner', auth()->user()->id)->get()->count(),
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
            if ($requestData['sale_id']) {
                $sale = $this->getSaleModel()->find(current(Hashids::decode($requestData['sale_id'])));

                $sale['hours']      = (new Carbon($sale['start_date']))->format('H:m:s');
                $sale['start_date'] = (new Carbon($sale['start_date']))->format('d/m/Y');

                if ($sale->flag == 'visa') {
                    $sale['flag_image'] = asset('modules/global/assets/img/cartoes/visa.png');
                } else if ($sale->flag == 'mastercard') {
                    $sale['flag_image'] = asset('modules/global/assets/img/cartoes/master.png');
                } else if ($sale->payment_method == 2) {
                    $sale['flag_image'] = asset('modules/global/assets/img/cartoes/boleto.png');
                } else {
                    $sale['flag_image'] = asset('modules/global/assets/img/cartoes/generico.png');
                }

                $client              = $this->getClient()->find($sale->client);
                $client['telephone'] = preg_replace("/[^0-9]/", "", $client['telephone']);

                $plansSales = $this->getPlansSales()->with(['plan.products'])->where('sale', $sale->id)
                                   ->get();

                $plans = [];
                $total = 0;

                foreach ($plansSales as $key => $planSale) {
                    $plans[$key]['name']   = $this->getPlan()->find($planSale['plan'])->name;
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

                $taxa = '000';
                if ($sale->dolar_quotation != 0) {
                    $taxa = intval($total / $sale->dolar_quotation);
                }

                $delivery             = $this->getDelivery()->find($sale['delivery']);
                $checkout             = $this->getCheckout()->find($sale['checkout']);
                $sale->shipment_value = preg_replace('/[^0-9]/', '', $sale->shipment_value);

                $userCompanies = $this->getCompany()->where('user_id', auth()->user()->id)->pluck('id');
                $transaction   = $this->getTransaction()->where('sale', $sale->id)->whereIn('company', $userCompanies)
                                      ->first();

                if ($transaction) {
                    $value = $transaction->value;
                } else {
                    $value = '000';
                }

                $details = view('sales::details', [
                    'sale'           => $sale,
                    'plans'          => $plans,
                    'client'         => $client,
                    'delivery'       => $delivery,
                    'checkout'       => $checkout,
                    'total'          => number_format(intval($total) / 100, 2, ',', '.'),
                    'subTotal'       => number_format(intval($subTotal) / 100, 2, ',', '.'),
                    'discount'       => number_format(intval($discount) / 100, 2, ',', '.'),
                    'shipment_value' => number_format(intval($sale->shipment_value) / 100, 2, ',', '.'),
                    'whatsapp_link'  => "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $client->telephone),
                    'comission'      => ($sale->dolar_quotation == '' ? 'R$ ' : 'US$ ') . substr_replace($value, '.', strlen($value) - 2, 0),
                    'taxa'           => number_format($taxa / 100, 2, ',', '.'),
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
            $sales = $this->getSaleModel()->where([['owner', auth()->user()->id], ['status', '!=', 3]]);

            if ($request->projeto != '') {
                $plans    = $this->getPlan()->where('project', $request->projeto)->pluck('id');
                $salePlan = $this->getPlansSales()->whereIn('plan', $plans)->pluck('sale');
                $sales->whereIn('id', $salePlan);
            }

            if ($request->comprador != '') {
                $clientes = $this->getClient()->where('name', 'LIKE', '%' . $request->comprador . '%')->pluck('id');
                $sales->whereIn('client', $clientes);
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

            $sales->orderBy('id', 'DESC'); 

            return SalesResource::collection($sales->paginate(10));
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

            //$sales = Sale::where('owner',\Auth::user()->id)->orWhere('affiliate',\Auth::user()->id);
            $sales = $this->getSaleModel()
                          ->where('owner', auth()->user()->id);
            //->where('status', '!=', '3');

            if (!empty($dataRequest['select_project'])) {
                $plans    = Plan::where('project', $dataRequest['select_project'])->pluck('id');
                $salePlan = PlanSale::whereIn('plan', $plans)->pluck('sale');
                $sales->whereIn('id', $salePlan);
            }

            if (!empty($dataRequest['client'])) {
                $clientes = Client::where('name', 'LIKE', '%' . $dataRequest['client'] . '%')->pluck('id');
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

            $header   = [
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
                $checkout  = Checkout::find($sale->checkout);
                $shipping  = Shipping::find($sale->shipping);
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



