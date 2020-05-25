<?php

namespace Modules\Notazz\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Core\Entities\Project;
use Modules\Notazz\Http\Requests\NotazzStoreRequest;
use Modules\Notazz\Http\Requests\NotazzUpdateRequest;
use Modules\Notazz\Transformers\NotazzInvoiceResource;
use Modules\Notazz\Transformers\NotazzResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class NotazzApiController
 * @package Modules\Notazz\Http\Controllers
 */
class NotazzApiController extends Controller
{
    /**
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $notazzIntegrationModel = new NotazzIntegration();

            $notazzIntegrations = $notazzIntegrationModel->with(['project', 'project.usersProjects'])
                ->whereHas(
                    'project.usersProjects',
                    function ($query) {
                        $query->where('user_id', auth()->user()->account_owner_id);
                    }
                )->get();

            return NotazzResource::collection($notazzIntegrations);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro ao listar as integrações com a notazz'], 400);
        }
    }

    /**
     *
     */
    public function create()
    {
        //
    }

    /**
     * @param NotazzStoreRequest $request
     * @return JsonResponse
     */
    public function store(NotazzStoreRequest $request)
    {
        try {
            $data = $request->all();
            $notazzIntegrationModel = new NotazzIntegration();
            $projectModel = new Project();

            $projectId = current(Hashids::decode($data['select_projects_create']));
            if ($projectId) {
                //hash ok

                $project = $projectModel->find($projectId);

                if (Gate::allows('show', [$project])) {
                    $integration = $notazzIntegrationModel->where('project_id', $projectId)->first();
                    if ($integration) {
                        return response()->json(
                            [
                                'message' => 'Projeto já integrado',
                            ],
                            400
                        );
                    }
                    $integrationCreated = $notazzIntegrationModel->create(
                        array_filter(
                            [
                                'token_api' => $data['token_api_create'],
                                'invoice_type' => $data['select_invoice_type_create'],
                                'token_webhook' => $data['token_webhook_create'],
                                'token_logistics' => $data['token_logistics_create'] ?? null,
                                'project_id' => $projectId,
                                'user_id' => auth()->user()->account_owner_id,
                                'start_date' => $data['start_date_create'],
                                'pending_days' => $data['select_pending_days_create'],
                                'discount_plataform_tax_flag' => $data['remove_tax'] ?? 0,
                                'generate_zero_invoice_flag' => $data['emit_zero'] ?? 0,
                            ],
                            function ($value) {
                                return !is_null($value);
                            }
                        )
                    );
                    if ($integrationCreated) {
                        if (!empty($data['start_date_create'])) {
                            return response()->json(
                                [
                                    'message' => 'Integração criada com sucesso e as notas fiscais foram agendadas para serem geradas.',
                                ],
                                200
                            );
                        } else {
                            return response()->json(
                                [
                                    'message' => 'Integração criada com sucesso!',
                                ],
                                200
                            );
                        }
                    } else {
                        return response()->json(
                            [
                                'message' => 'Ocorreu um erro ao realizar a integração',
                            ],
                            400
                        );
                    }
                } else {
                    return response()->json('Sem permissão para remover projeto', 403);
                }
            } else {
                //hash wrong
                return response()->json(
                    [
                        'message' => 'Projeto não encontrado',
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @param $integrationCode
     * @return JsonResponse|NotazzResource
     */
    public function show($integrationCode)
    {
        try {
            $notazzIntegrationModel = new NotazzIntegration();

            $integrationId = current(Hashids::decode($integrationCode));

            if ($integrationId) {
                //hash ok
                $notazzIntegration = $notazzIntegrationModel->with(['project', 'project.usersProjects'])
                    ->whereHas(
                        'project.usersProjects',
                        function ($query) {
                            $query->where('user_id', auth()->user()->account_owner_id);
                        }
                    )->find($integrationId);

                return new NotazzResource($notazzIntegration);
            } else {
                //hash wrong
                return response()->json(['message' => 'Ocorreu um erro ao listar a integração com a notazz'], 400);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu um erro ao listar a integração com a notazz'], 400);
        }
    }

    /**
     * @param $id
     */
    public function edit($id)
    {
        //
    }

    /**
     * @param NotazzUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(NotazzUpdateRequest $request, $id)
    {
        try {
            $notazzIntegrationModel = new NotazzIntegration();

            $integrationId = current(Hashids::decode($id));

            if ($integrationId) {
                //hash ok

                $dataRequest = $request->validated();

                $integrationNotazz = $notazzIntegrationModel->find($integrationId);

                $integrationNotazz->update(
                    array_filter(
                        [
                            'token_webhook' => $dataRequest['token_webhook_edit'],
                            'token_api' => $dataRequest['token_api_edit'],
                            'token_logistics' => $dataRequest['token_logistics_edit'],
                            'pending_days' => $dataRequest['select_pending_days_edit'],
                            'discount_plataform_tax_flag' => $dataRequest['remove_tax_edit'] ?? null,
                            'generate_zero_invoice_flag' => $dataRequest['emit_zero_edit'] ?? null,
                            'active_flag' => $dataRequest['active_flag'] ?? null,
                        ],
                        function ($value) {
                            return !is_null($value);
                        }
                    )
                );

                return response()->json(
                    [
                        'message' => 'Integração atualizada com sucesso.',
                    ],
                    200
                );
            } else {
                //hash error

                return response()->json(
                    [
                        'message' => 'Integração não encontrada',
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Integração não encontrada',
                ],
                400
            );
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $notazzIntegrationModel = new NotazzIntegration();

            $integrationId = current(Hashids::decode($id));

            if ($integrationId) {
                //hash ok

                $integration = $notazzIntegrationModel->with(['invoices'])->find($integrationId);
                if ($integration) {
                    if (count($integration->invoices) == 0) {
                        $integrationDeleted = $integration->delete();
                        if ($integrationDeleted) {
                            //integracao removida

                            return response()->json(
                                [
                                    'message' => 'Integração removida com sucesso.',
                                ],
                                200
                            );
                        } else {
                            //erro ao remover integracao
                            return response()->json(
                                [
                                    'message' => 'Erro ao remover integração',
                                ],
                                400
                            );
                        }
                    } else {
                        return response()->json(
                            [
                                'message' => 'Não é possivel remover a integração, pois o já existem notas fiscais geradas ',
                            ],
                            400
                        );
                    }
                } else {
                    return response()->json(
                        [
                            'message' => 'Integração não encontrada',
                        ],
                        400
                    );
                }
            } else {
                //hash error
                return response()->json(
                    [
                        'message' => 'Integração não encontrada',
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Integração não encontrada',
                ],
                400
            );
        }
    }

    /**
     * @param $code
     * @return JsonResponse|NotazzInvoiceResource
     */
    public function getInvoice($code)
    {
        try {
            $notazzInvoiceModel = new NotazzInvoice();

            $notazzInvoiceId = current(Hashids::decode($code));

            if ($notazzInvoiceId) {
                //hash ok
                $notazzInvoice = $notazzInvoiceModel->find($notazzInvoiceId);

                if ($notazzInvoice) {
                    return new NotazzInvoiceResource($notazzInvoice);
                } else {
                    return response()->json(
                        [
                            'data' => [],
                        ],
                        200
                    );
                }
            } else {
                //erro no hash
                return response()->json(
                    [
                        'data' => [],
                    ],
                    200
                );
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar integraçeõs da Notazz (NotazzController - getInvoice)');
            report($e);

            return response()->json(
                [
                    'data' => [],
                ],
                200
            );
        }
    }
}
