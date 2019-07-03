<?php

namespace Modules\Sales\Http\Controllers;

use App\Entities\Shipping;
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
use Maatwebsite\Excel\Facades\Excel;
use Modules\Sales\Exports\Reports\SaleReportExport;
use function MongoDB\BSON\toJSON;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use Modules\Sales\Transformers\SalesResource;

class SalesController extends Controller
{
    public function index()
    {

        $userProjects = UserProject::where('user', \Auth::user()->id)->get()->toArray();
        $projects     = [];

        foreach ($userProjects as $userProject) {
            $project = Project::find($userProject['project']);
            if ($project['id'] != null) {
                $projects[] = [
                    'id'   => $project['id'],
                    'nome' => $project['name'],
                ];
            }
        }

        return view('sales::index', [
            'projetos'     => $projects,
            'sales_amount' => Sale::where('owner', auth()->user()->id)->get()->count(),
        ]);
    }

    public function getSaleDetail(Request $request)
    {

        $requestData = $request->all();
        $sale        = Sale::find(current(Hashids::decode($requestData['sale_id'])));

        $plansSales = PlanSale::where('sale', $sale->id)->get()->toArray();
        $plans      = [];

        foreach ($plansSales as $key => $planSale) {
            $plans[$key]['name']   = Plan::find($planSale['plan'])['name'];
            $plans[$key]['amount'] = $planSale['amount'];
        }

        $client   = Client::find($sale->client);
        $delivery = Delivery::find($sale->delivery);

        $sale['start_date'] = (new Carbon($sale['start_date']))->format('d/m/Y H:i:s');

        $client['telephone'] = preg_replace("/[^0-9]/", "", $client['telephone']);

        $checkout = Checkout::find($sale['checkout']);

        $details = view('sales::details', [
            'sale'     => $sale,
            'plans'    => $plans,
            'client'   => $client,
            'delivery' => $delivery,
            'checkout' => $checkout,
        ]);

        return response()->json($details->render());
    }

    public function refundSale(Request $request)
    {

        return response()->json('sucesso');
    }

    public function getSales(Request $request)
    {

        //$sales = Sale::where('owner',\Auth::user()->id)->orWhere('affiliate',\Auth::user()->id);
        $sales = Sale::where('owner', \Auth::user()->id);

        $sales = $sales->where('status', '!=', '3');

        if ($request->projeto != '') {
            $plans    = Plan::where('project', $request->projeto)->pluck('id');
            $salePlan = PlanSale::whereIn('plan', $plans)->pluck('sale');
            $sales->whereIn('id', $salePlan);
        }

        if ($request->comprador != '') {
            $clientes = Client::where('name', 'LIKE', '%' . $request->comprador . '%')->pluck('id');
            $sales->whereIn('client', $clientes);
        }

        if ($request->forma != '') {
            $sales->where('payment_form', $request->forma);
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
    }

    public function getCsvSales(Request $request)
    {

        try {

            //$sales = Sale::where('owner',\Auth::user()->id)->orWhere('affiliate',\Auth::user()->id);
            $sales = Sale::where('owner', \Auth::user()->id);

            $sales = $sales->where('status', '!=', '3');

            if ($request->projeto != '') {
                $plans    = Plan::where('project', $request->projeto)->pluck('id');
                $salePlan = PlanSale::whereIn('plan', $plans)->pluck('sale');
                $sales->whereIn('id', $salePlan);
            }

            if ($request->comprador != '') {
                $clientes = Client::where('name', 'LIKE', '%' . $request->comprador . '%')->pluck('id');
                $sales->whereIn('client', $clientes);
            }

            if ($request->forma != '') {
                $sales->where('payment_form', $request->forma);
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



