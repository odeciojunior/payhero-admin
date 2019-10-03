<?php

namespace Modules\Sales\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Client;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\Transaction;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Sales\Exports\Reports\SaleReportExport;
use Modules\Sales\Transformers\SalesResource;
use Modules\Sales\Transformers\TransactionResource;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SalesApiController
 * @package Modules\Sales\Http\Controllers
 */
class SalesApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
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

            if (!empty($data["project"])) {
                $projectId = current(Hashids::decode($data["project"]));
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

            if (!empty($data["client"])) {
                $customers = $clientModel->where('name', 'LIKE', '%' . $data["client"] . '%')->pluck('id');
                $transactions->whereHas('sale', function ($querySale) use ($customers) {
                    $querySale->whereIn('client_id', $customers);
                });
            }

            if (!empty($data["payment_method"])) {
                $forma = $data["payment_method"];
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

            if (!empty($data["start_date"]) && !empty($data["end_date"])) {
                $start_date = $data["start_date"];
                $end_date = $data["end_date"];
                $transactions->whereHas('sale', function ($querySale) use ($start_date, $end_date) {
                    $querySale->whereBetween('start_date', [$start_date, date('Y-m-d', strtotime($end_date . ' + 1 day'))]);
                });
            } else {

                if (!empty($data["start_date"])) {
                    $start_date = $data["start_date"];
                    $transactions->whereHas('sale', function ($querySale) use ($start_date) {
                        $querySale->whereDate('start_date', '>=', $start_date);
                    });
                }

                if (!empty($data["end_date"])) {
                    $end_date = $data["end_date"];
                    $transactions->whereHas('sale', function ($querySale) use ($end_date) {
                        $querySale->whereDate('end_date', '<', date('Y-m-d', strtotime($end_date . ' + 1 day')));
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
     * @param $id
     * @return JsonResponse|SalesResource
     */
    public function show($id)
    {
        try {
            $saleModel = new Sale();

            if (isset($id)) {
                $sale = $saleModel->with([
                    'transactions' => function ($query) {
                        $query->where('company_id', '!=', null)->first();
                    },
                ])->find(current(Hashids::connection('sale_id')->decode($id)));

                return new SalesResource($sale);
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
     * @return JsonResponse|BinaryFileResponse
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

            return Excel::download(new SaleReportExport($saleData, $header, 16), 'export.' . $dataRequest['format']);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao tentar gerar o arquivo Excel.'], 200);
        }
    }
}
