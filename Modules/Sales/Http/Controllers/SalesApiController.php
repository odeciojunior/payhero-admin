<?php

namespace Modules\Sales\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\SaleService;
use Modules\Sales\Exports\Reports\SaleReportExport;
use Modules\Sales\Http\Requests\SaleIndexRequest;
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
     * @param SaleIndexRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(SaleIndexRequest $request)
    {
        try {

            $saleService = new SaleService();

            $data = $request->all();

            $sales = $saleService->getSales($data);

            return TransactionResource::collection($sales);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar vendas SalesApiController - index');
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
            $saleService = new SaleService();

            if (isset($id)) {
                $sale = $saleService->getSaleWithDetails($id);


                return new SalesResource($sale);
            }

            return response()->json(['error' => 'Erro ao exibir detalhes da venda'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao mostrar detalhes da venda  SalesApiController - show');
            report($e);

            return response()->json(['error' => 'Erro ao exibir detalhes da venda'], 400);
        }
    }

    /**
     * @param SaleIndexRequest $request
     * @return JsonResponse|BinaryFileResponse
     */
    public function export(SaleIndexRequest $request)
    {
        try {
            $dataRequest = $request->all();

            $saleService = new SaleService();

            $salesResult = $saleService->getSales($dataRequest, false)->map(
                function ($transaction) {
                    return $transaction->sale;
                }
            );

            $header = [
                //sale
                'Projeto',
                'Código da Venda',
                'Forma de Pagamento',
                'Número de Parcelas',
                'Bandeira do Cartão',
                'Link do Boleto',
                'Linha Digitavel do Boleto',
                'Data de Vencimento do Boleto',
                'Data Inicial do Pagamento',
                'Data Final do Pagamento',
                'Data da Criação da  Venda',
                'Status',
                'Iof',
                'Desconto Shopify',
                'Frete',
                'Valor do Frete',
                'Cotação do dolar',
                'Valor Total Venda',
                //client
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
                //track
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
                $saleArray = [
                    //sale
                    'project_name' => $sale->project->name ?? '',
                    'sale_code' => '#' . strtoupper(Hashids::connection('sale_id')
                            ->encode($sale->id)),
                    'payment_form' => $sale->payment_method == 2 ? 'Boleto' : ($sale->payment_method == 1 ? 'Cartão' : ''),
                    'installments_amount' => $sale->installments_amount ?? '',
                    'flag' => $sale->flag ?? '',
                    'boleto_link' => $sale->boleto_link ?? '',
                    'boleto_digitable_line' => $sale->boleto_digitable_line ?? '',
                    'boleto_due_date' => $sale->boleto_due_date ? Carbon::parse($sale->boleto_due_date)->format('d/m/Y') : '',
                    'start_date' => $sale->start_date ? Carbon::parse($sale->start_date)->format('d/m/Y H:i:s') : '',
                    'end_date' => $sale->end_date ? Carbon::parse($sale->end_date)->format('d/m/Y H:i:s') : '',
                    'created_at' => $sale->created_at ? Carbon::parse($sale->created_at)->format('d/m/Y H:i:s') : '',
                    'status' => $sale->present()->getStatus(),
                    'iof' => $sale->iof ?? '',
                    'shopify_discount' => $sale->shopify_discount ?? '',
                    'shipping' => $sale->shipping->name ?? '',
                    'shipping_value' => $sale->shipping->value ?? '',
                    'dolar_quotation' => $sale->dolar_quotation ?? '',
                    'total_paid' => $sale->total_paid_value ?? '',
                    //client
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
                    //track
                    'src' => $sale->checkout->src ?? '',
                    'utm_source' => $sale->checkout->utm_source ?? '',
                    'utm_medium' => $sale->checkout->utm_medium ?? '',
                    'utm_campaign' => $sale->checkout->utm_campaign ?? '',
                    'utm_term' => $sale->checkout->utm_term ?? '',
                    'utm_content' => $sale->checkout->utm_content ?? '',
                ];

                $saleData->push(collect($saleArray));
            }

            return Excel::download(new SaleReportExport($saleData, $header, 16), 'export.' . $dataRequest['format']);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar gerar o arquivo Excel.'], 200);
        }
    }

    public function resume(SaleIndexRequest $request)
    {
        try {
            $saleService = new SaleService();

            $data = $request->all();

            $transactions = $saleService->getSales($data, false);

            if ($transactions->count()) {
                $resume = $transactions->reduce(function ($carry, $item) use ($saleService) {
                    //quantidade de vendas
                    $carry['total_sales'] += 1;
                    //cria um item no array pra cada moeda inclusa nas vendas
                    $item->currency = $item->currency ?? 'real';
                    $carry[$item->currency] = $carry[$item->currency] ?? ['comission' => 0, 'total' => 0];
                    //comissao
                    $carry[$item->currency]['comission'] += in_array($item->status,['paid', 'transfered', 'anticipated']) ? (floatval($item->value) / 100) : 0;
                    //calcula o total
                    $total = $item->sale->sub_total;
                    $total +=  $item->sale->shipment_value;
                    $shopify_discount = floatval($item->sale->shopify_discount) / 100;
                    if ($shopify_discount > 0) {
                        $total -= $shopify_discount;
                    }
                    if ($item->sale->dolar_quotation != 0) {
                        $iof = preg_replace('/[^0-9]/', '', $item->sale->iof);
                        $iof = substr_replace($iof, '.', strlen($iof) - 2, 0);
                        $total += floatval($iof);
                    }
                    $carry[$item->currency]['total'] += $total;
                    return $carry;
                }, ['total_sales' => 0]);

                //formata os valores
                foreach ($resume as &$item) {
                    if (is_array($item)) {
                        foreach ($item as &$value) {
                            $value = number_format($value, 2, ',', '.');
                        }
                    }
                }
                return response()->json($resume);
            } else {
                return response()->json([]);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao exibir resumo das venda  SalesApiController - resume');
            report($e);

            return response()->json(['error' => 'Erro ao exibir resumo das vendas'], 400);
        }
    }
}
