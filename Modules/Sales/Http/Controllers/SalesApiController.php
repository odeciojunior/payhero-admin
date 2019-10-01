<?php

namespace Modules\Sales\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Client;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Delivery;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\Transaction;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\TrackingService;
use Modules\Sales\Exports\Reports\SaleReportExport;
use Modules\Sales\Http\Requests\SaleUpdateRequest;
use Modules\Sales\Transformers\TransactionResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SalesApiController
 * @package Modules\Sales\Http\Controllers
 */
class SalesApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function filters()
    {
        try {
            $transactionModel = new Transaction();
            $companyModel = new Company();
            $projectService = new ProjectService();

            $myProjects = $projectService->getUserProjects();

            $userCompanies = $companyModel->where('user_id', auth()->user()->id)
                ->pluck('id')
                ->toArray();

            $projects = [];

            foreach ($myProjects as $project) {
                if ($project != null) {
                    $projects[] = [
                        'id' => Hashids::encode($project->id),
                        'nome' => $project->name,
                    ];
                }
            }

            $sales_amount = $transactionModel->whereIn('company_id', $userCompanies)
                ->get()
                ->count();

            return response()->json([
                'projetos' => $projects,
                'sales_amount' => $sales_amount,
            ], 200);

        } catch (Exception $e) {
            Log::warning('Erro ao carregar filtros das vendas - filters');
            report($e);
            return response()->json(['message' => 'Erro ao carregar filtros das vendas'], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function show($id)
    {
        try {
            $saleModel = new Sale();
            $clientModel = new Client();
            $deliveryModel = new Delivery();
            $checkoutModel = new Checkout();
            $companyModel = new Company();
            $transactionModel = new Transaction();
            $trackingSerive = new TrackingService();

            if (isset($id)) {
                $sale = $saleModel->with([
                    'transactions' => function ($query) {
                        $query->where('company_id', '!=', null)->first();
                    },
                ])->find(Hashids::connection('sale_id')->decode($id)[0]);

                $sale['hours'] = (new Carbon($sale['start_date']))->format('H:m:s');

                $sale['start_date'] = (new Carbon($sale['start_date']))->format('d/m/Y');

                if (isset($sale['boleto_due_date'])) {
                    $sale['boleto_due_date'] = (new Carbon($sale['boleto_due_date']))->format('d/m/Y');
                }

                if ($sale->flag) {
                    $sale['flag'] = $sale->flag;
                } else if ((!$sale->flag || empty($sale->flag)) && $sale->payment_method == 1) {
                    $sale['flag'] = 'generico';
                } else {
                    $sale['flag'] = 'boleto';
                }

                $client = $clientModel->find($sale->client_id);
                if (!empty($client['telephone'])) {
                    $client['telephone'] = preg_replace("/[^0-9]/", "", $client['telephone']);
                } else {
                    $client['telephone'] = '';
                }

                //                $products = $sale->present()->getProducts();

                $products = $trackingSerive->getTrackingProducts($sale);

                $discount = '0,00';
                $subTotal = $sale->present()->getSubTotal();
                $total = $subTotal;

                $total += preg_replace("/[^0-9]/", "", $sale->shipment_value);
                if (preg_replace("/[^0-9]/", "", $sale->shopify_discount) > 0) {
                    $total -= preg_replace("/[^0-9]/", "", $sale->shopify_discount);
                    $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
                } else {
                    $discount = '0,00';
                }

                $delivery = $deliveryModel->find($sale['delivery_id']);
                if (isset($delivery)) {
                    $delivery['code'] = Hashids::encode($delivery->id);
                }

                $checkout = $checkoutModel->find($sale['checkout_id']) ?? (object)[];
                $checkout->src = isset($checkout->src) ? $checkout->src : '';
                $checkout->source = isset($checkout->source) ? $checkout->source : '';
                $checkout->utm_medium = isset($checkout->utm_medium) ? $checkout->utm_medium : '';
                $checkout->utm_campaign = isset($checkout->utm_campaign) ? $checkout->utm_campaign : '';
                $checkout->utm_term = isset($checkout->utm_term) ? $checkout->utm_term : '';
                $checkout->utm_content = isset($checkout->utm_content) ? $checkout->utm_content : '';
                $sale->shipment_value = preg_replace('/[^0-9]/', '', $sale->shipment_value);

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

                $sale['code'] = Hashids::connection('sale_id')->encode($sale->id);
                $data = [
                    'sale' => $sale,
                    'products' => $products,
                    'client' => $client,
                    'delivery' => $delivery,
                    'checkout' => $checkout,
                    'total' => number_format(intval($total) / 100, 2, ',', '.'),
                    'subTotal' => number_format(intval($subTotal) / 100, 2, ',', '.'),
                    'discount' => number_format(intval($discount) / 100, 2, ',', '.'),
                    'shipment_value' => number_format(intval($sale->shipment_value) / 100, 2, ',', '.'),
                    'whatsapp_link' => $client->present()->getWhatsappMessage(),
                    'comission' => $comission,
                    'convertax_value' => $convertaxValue,
                    'taxa' => number_format($taxa / 100, 2, ',', '.'),
                    'taxaReal' => $taxaReal,
                    'transaction' => $transaction,
                ];

                return response()->json($data, 200);
            }
            return response()->json(['error' => 'Erro ao exibir detalhes da venda'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao mostrar detalhes da venda  SalesController - details');
            report($e);
            return response()->json(['error' => 'Erro ao exibir detalhes da venda'], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
//    public function refundSale(Request $request)
//    {
//        return response()->json('sucesso');
//    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $companyModel = new Company();
            $clientModel = new Client();
            $transactionModel = new Transaction();

            $data = $request->all();

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
            ])
                ->whereHas('sale', function ($querySale) {
                    $querySale->whereNotIn('status', [3, 5, 10]);
                })
                ->whereIn('company_id', $userCompanies);

            if (!empty($data["projeto"])) {
                $projectId = current(Hashids::decode($data["projeto"]));
                $transactions->whereHas('sale', function ($querySale) use ($projectId) {
                    $querySale->where('project_id', $projectId);
                });
            }

            if (!empty($data["transaction"])) {
                $saleId = current(Hashids::connection('sale_id')->decode(str_replace('#', '', $data["transaction"])));

                $transactions->whereHas('sale', function ($querySale) use ($saleId) {
                    $querySale->where('id', $saleId);
                });
            }

            if (!empty($data["comprador"])) {
                $customers = $clientModel->where('name', 'LIKE', '%' . $data["comprador"] . '%')->pluck('id');
                $transactions->whereHas('sale', function ($querySale) use ($customers) {
                    $querySale->whereIn('client_id', $customers);
                });
            }

            if (!empty($data["forma"])) {
                $forma = $data["forma"];
                $transactions->whereHas('sale', function ($querySale) use ($forma) {
                    $querySale->where('payment_method', $forma);
                });
            }

            if (!empty($data["status"])) {
                $status = $data["status"];
                $transactions->whereHas('sale', function ($querySale) use ($status) {
                    $querySale->where('status', $status);
                });
            }

            if (!empty($data["data_inicial"]) && !empty($data["data_final"])) {
                $data_inicial = $data["data_inicial"];
                $data_final = $data["data_final"];
                $transactions->whereHas('sale', function ($querySale) use ($data_inicial, $data_final) {
                    $querySale->whereBetween('start_date', [$data_inicial, date('Y-m-d', strtotime($data_final . ' + 1 day'))]);
                });
            } else {

                if (!empty($data["data_inicial"])) {
                    $data_inicial = $data["data_inicial"];
                    $transactions->whereHas('sale', function ($querySale) use ($data_inicial) {
                        $querySale->whereDate('start_date', '>=', $data_inicial);
                    });
                }

                if (!empty($data["data_final"])) {
                    $data_final = $data["data_final"];
                    $transactions->whereHas('sale', function ($querySale) use ($data_final) {
                        $querySale->whereDate('end_date', '<', date('Y-m-d', strtotime($data_final . ' + 1 day')));
                    });
                }
            }

            return TransactionResource::collection($transactions->orderBy('id', 'DESC')->paginate(10));
        } catch (Exception $e) {
            Log::warning('Erro ao buscar vendas SalesController - getSales');
            report($e);
            return response()->json(['message' => 'Erro ao carregar vendas'], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        try {
            $dataRequest = $request->all();
            $dataRequest = array_filter($dataRequest);

            $saleModel = new Sale();
            $planSaleModel = new PlanSale();
            $clientModel = new Client();
            $planModel = new Plan();
            $checkoutModel = new Checkout();
            $shippingModel = new Shipping();

            $sales = $saleModel->where('owner_id', auth()->user()->id);

            if (!empty($dataRequest['select_project'])) {
                $plans = $planModel->where('project', $dataRequest['select_project'])->pluck('id');
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

            $sales->with(['client', 'project', 'plansSales', 'user', 'affiliate', 'delivery'])//'shippingModel', , 'checkoutModel'
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
                $checkout = $checkoutModel->find($sale->checkout->id);
                $shipping = $shippingModel->find($sale->shipping->id);
                $saleArray = [
                    'project_name' => $sale->project->name ?? '',
                    'sale_code' => '#' . strtoupper(Hashids::connection('sale_id')
                            ->encode($sale->id)),
                    'owner' => $sale->user->name ?? '',
                    'affiliate' => null,
                    'payment_form' => $sale->payment_form ?? '',
                    //'payment_method' => ($sale->payment_method == 1) ? "credit_card" : "boleto",
                    'installments_amount' => $sale->installments_amount ?? '',
                    'installments_value' => $sale->installments_value ?? '',
                    'flag' => $sale->flag ?? '',
                    'boleto_link' => $sale->boleto_link ?? '',
                    'boleto_digitable_line' => $sale->boleto_digitable_line ?? '',
                    'boleto_due_date' => $sale->boleto_due_date ?? '',
                    'start_date' => $sale->start_date ?? '',
                    'end_date' => $sale->end_date ?? '',
                    'created_at' => $sale->created_at ?? '',
                    'status' => $sale->status ?? '',
                    'gateway_status' => $sale->gateway_status ?? '',
                    'iof' => $sale->iof ?? '',
                    'shopify_discount' => $sale->shopify_discount ?? '',
                    'shipping' => $shipping->name ?? '',
                    'shipping_value' => $shipping->value ?? '',
                    'dolar_quotation' => $sale->dolar_quotation ?? '',
                    'total_paid' => $sale->total_paid_value ?? '',
                    'client_name' => $sale->client->name ?? '',
                    'client_telephone' => $sale->client->telephone ?? '',
                    'client_email' => $sale->client->email ?? '',
                    'client_document' => $sale->client->document ?? '',
                    'client_street' => $sale->delivery->street ?? '',
                    'client_number' => $sale->delivery->number ?? '',
                    'client_complement' => $sale->delivery->complement ?? '',
                    'client_neighborhood' => $sale->delivery->neighborhood ?? '',
                    'client_zip_code' => $sale->delivery->zip_code ?? '',
                    'client_city' => $sale->delivery->city ?? '',
                    'client_state' => $sale->delivery->state ?? '',
                    'client_country' => $sale->delivery->country ?? '',
                    'src' => $checkout->src ?? '',
                    'utm_source' => $checkout->utm_source ?? '',
                    'utm_medium' => $checkout->utm_medium ?? '',
                    'utm_campaign' => $checkout->utm_campaign ?? '',
                    'utm_term' => $checkout->utm_term ?? '',
                    'utm_content' => $checkout->utm_content ?? '',
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

            return Excel::download(new SaleReportExport($saleData, $header, 16), 'export.' . $dataRequest['format']);
        } catch (Exception $e) {
            report($e);

            return redirect()->back()->with('error', 'Erro ao tentar gerar o arquivo Excel . ');
        }
    }
}
