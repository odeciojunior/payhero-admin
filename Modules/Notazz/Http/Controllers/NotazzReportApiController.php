<?php

namespace Modules\Notazz\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Core\Services\FoxUtils;
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try {
            dd('123');
        } catch (Exception $e) {
            Log::warning('Erro ao buscar integraçeõs da Notazz (NotazzReportApiController - index)');
            report($e);

            return response()->json(['message' => 'Ocorreu um erro ao listar as notas fiscais'], 400);
        }
    }

    /**
     *
     */
    public function create()
    {
        dd('123');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(NotazzStoreRequest $request)
    {

        try {
            dd('123');
        } catch (Exception $e) {
            Log::warning('Erro ao realizar integração  NotazzReportApiController - store');
            report($e);
        }
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
        dd('123');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            dd('123');
        } catch (Exception $e) {
            Log::warning('Erro ao atualizar integraçeõs da Notazz (NotazzReportApiController - update)');
            report($e);

            return response()->json([
                                        'message' => 'Nota fiscal não encontrada',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            dd('123');
        } catch (Exception $e) {
            Log::warning('Erro ao buscar integraçeõs da Notazz (NotazzReportApiController - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Nota fiscal não encontrada',
                                    ], 400);
        }
    }
}
