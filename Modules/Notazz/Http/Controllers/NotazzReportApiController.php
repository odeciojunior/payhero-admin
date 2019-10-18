<?php

namespace Modules\Notazz\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Core\Services\FoxUtils;
use Modules\Notazz\Exports\Reports\InvoiceReportExport;
use Modules\Notazz\Http\Requests\NotazzStoreRequest;
use Modules\Notazz\Transformers\NotazzInvoiceReportResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class NotazzApiController
 * @package Modules\Notazz\Http\Controllers
 */
class NotazzReportApiController extends Controller
{
    /**
     *
     */
    public function index()
    {

    }

    /**
     *
     */
    public function create()
    {
    }

    /**
     * @param NotazzStoreRequest $request
     */
    public function store(NotazzStoreRequest $request)
    {

    }

    /**
     * @param Request $request
     * @param $notazzIntegrationCode
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show(Request $request, $notazzIntegrationCode)
    {
        try {
            $notazzIntegrationModel = new NotazzIntegration();
            $notazzInvoiceModel     = new NotazzInvoice();

            $dataForm = $request->all();

            $notazzIntegrationId = current(Hashids::decode($notazzIntegrationCode));

            if ($notazzIntegrationId) {
                //hash ok
                $notazzIntegration = $notazzIntegrationModel->find($notazzIntegrationId);

                if (Gate::allows('show', [$notazzIntegration])) {

                    $notazzInvoices = $notazzInvoiceModel->with([
                                                                    'sale.project',
                                                                    'sale.plansSales',
                                                                    'sale.client',
                                                                ])
                                                         ->where('notazz_integration_id', $notazzIntegration->id);

                    if (!empty($dataForm['date_range'])) {
                        $dateRange = FoxUtils::validateDateRange($dataForm["date_range"]);
                        $notazzInvoices->whereBetween('created_at', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
                    }

                    if (!empty($dataForm['status'])) {
                        $status = $dataForm['status'];
                        $notazzInvoices->where('status', $status);
                    }

                    if (!empty($dataForm['client'])) {
                        $clientName = $dataForm['client'];
                        $notazzInvoices->whereHas('sale.client', function($queryClient) use ($clientName) {
                            $queryClient->where('name', 'LIKE', '%' . $clientName . '%');
                        });
                    }

                    if (!empty($dataForm['transaction'])) {
                        $dataForm['transaction'] = str_replace('#', "", $dataForm['transaction']);

                        $saleId = current(Hashids::connection('sale_id')->decode($dataForm['transaction']));
                        $notazzInvoices->whereHas('sale', function($querySale) use ($saleId) {
                            $querySale->where('id', $saleId);
                        });
                    }

                    $notazzInvoices = $notazzInvoices->paginate(10);

                    return NotazzInvoiceReportResource::collection($notazzInvoices);
                } else {
                    return response()->json(['message' => 'Sem permissão para listar as notas fiscais'], 400);
                }
            } else {
                //hash wrong
                return response()->json(['message' => 'Ocorreu um erro ao listar as notas ficais'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar integração da Notazz (NotazzReportApiController - show)');
            report($e);

            return response()->json(['message' => 'Ocorreu um erro ao listar as notas ficais'], 400);
        }
    }

    /**
     * @param $id
     */
    public function edit($id)
    {
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * @param $id
     */
    public function destroy($id)
    {

    }

    /**
     * @param Request $request
     * @param $notazzIntegrationCode
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function invoicesExport(Request $request, $notazzIntegrationCode)
    {
        try {
            $notazzIntegrationModel = new NotazzIntegration();
            $notazzInvoiceModel     = new NotazzInvoice();

            $dataForm = $request->all();

            $notazzIntegrationId = current(Hashids::decode($notazzIntegrationCode));

            if ($notazzIntegrationId) {
                //hash ok
                $notazzIntegration = $notazzIntegrationModel->find($notazzIntegrationId);

                if (Gate::allows('show', [$notazzIntegration])) {

                    $notazzInvoices = $notazzInvoiceModel->with([
                                                                    'sale.project',
                                                                    'sale.plansSales',
                                                                    'sale.client',
                                                                ])
                                                         ->where('notazz_integration_id', $notazzIntegration->id);

                    if (!empty($dataForm['date_range'])) {
                        $dateRange = FoxUtils::validateDateRange($dataForm["date_range"]);
                        $notazzInvoices->whereBetween('created_at', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
                    }

                    if (!empty($dataForm['status'])) {
                        $status = $dataForm['status'];
                        $notazzInvoices->where('status', $status);
                    }

                    if (!empty($dataForm['client'])) {
                        $clientName = $dataForm['client'];
                        $notazzInvoices->whereHas('sale.client', function($queryClient) use ($clientName) {
                            $queryClient->where('name', 'LIKE', '%' . $clientName . '%');
                        });
                    }

                    if (!empty($dataForm['transaction'])) {
                        $dataForm['transaction'] = str_replace('#', "", $dataForm['transaction']);

                        $saleId = current(Hashids::connection('sale_id')->decode($dataForm['transaction']));
                        $notazzInvoices->whereHas('sale', function($querySale) use ($saleId) {
                            $querySale->where('id', $saleId);
                        });
                    }

                    $notazzInvoices = $notazzInvoices->get();

                    $header = [
                        'Transação',
                        'Projeto',
                        'Produto',
                        'Cliente',
                        'Status',
                        'Data',
                        'Valor',
                        'Message Notazz',
                        'Message Postback Notazz',
                    ];

                    $invoiceData = collect();
                    foreach ($notazzInvoices as $invoice) {
                        $invoiceArray = [
                            'transaction'      => '#' . strtoupper(Hashids::connection('sale_id')
                                                                          ->encode($invoice->sale->id)),
                            'project'          => $invoice->sale->project->name ?? '',
                            'product'          => ($invoice->sale) ? ((count($invoice->sale->getRelation('plansSales')) > 1) ? 'Carrinho' : $invoice->sale->plansSales->first()->plan->name) : null,
                            'client'           => $invoice->sale->client->name,
                            'status_translate' => Lang::get('definitions.enum.invoices.status.' . $invoice->present()
                                                                                                          ->getStatus($invoice->status)),
                            'updated_date'     => ($invoice->updated_at) ? Carbon::parse($invoice->updated_at)
                                                                                 ->format('d/m/Y H:i:s') : null,
                            'value'            => $invoice->sale->sub_total,
                            'return_message'   => $invoice->return_message,
                            'postback_message' => $invoice->postback_message,
                        ];

                        $invoiceData->push(collect($invoiceArray));
                    }

                    return Excel::download(new InvoiceReportExport($invoiceData, $header, 16), 'export.' . $dataForm['format']);
                } else {
                    return response()->json(['message' => 'Sem permissão para listar as notas fiscais'], 400);
                }
            } else {
                //hash wrong
                return response()->json(['message' => 'Ocorreu um erro ao listar as notas ficais'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar integração da Notazz (NotazzReportApiController - show)');
            report($e);

            return response()->json(['message' => 'Ocorreu um erro ao listar as notas ficais'], 400);
        }
    }
}
