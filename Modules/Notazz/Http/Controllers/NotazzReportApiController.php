<?php

namespace Modules\Notazz\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Notazz\Http\Requests\NotazzStoreRequest;
use Modules\Notazz\Transformers\NotazzInvoiceResource;
use Modules\Notazz\Transformers\NotazzResource;
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
     * @param $integrationCode
     * @return \Illuminate\Http\JsonResponse|NotazzResource
     */
    public function show(Request $request, $id)
    {
        try {
            $notazzIntegrationModel = new NotazzIntegration();

            $dataForm = $request->all();

            $notazzIntegrationId = Hashids::decode($id)[0];

            $notazzIntegration = $notazzIntegrationModel->with(['project'])->find($notazzIntegrationId);

            if (Gate::allows('show', [$notazzIntegration])) {
                dd($dataForm);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar integração da Notazz (NotazzReportApiController - show)');
            report($e);

            return response()->json(['message' => 'Ocorreu um erro ao listar a nota fiscal'], 400);
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
