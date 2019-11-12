<?php

namespace Modules\DigitalManager\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\DigitalmanagerIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectService;
use Modules\DigitalManager\Transformers\DigitalmanagerResource;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Vinkla\Hashids\Facades\Hashids;

class DigitalManagerApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $digitalmanagerIntegration = new DigitalmanagerIntegration();
            $userProjectModel   = new UserProject();
            $projectModel       = new Project();

            $digitalmanagerIntegrations = $digitalmanagerIntegration->where('user_id',auth()->user()->account_owner_id)->with('project')->get();

            $projects     = collect();
            $userProjects = $userProjectModel->where('user_id', auth()->user()->account_owner_id)->get();
            if ($userProjects->count() > 0) {
                foreach ($userProjects as $userProject) {
                    $project = $userProject->project()->where('status', $projectModel->present()->getStatus('active'))
                                           ->first();
                    if (!empty($project)) {
                        $projects->add($userProject->project);
                    }
                }
            }

            return response()->json([
                                        'integrations' => DigitalmanagerResource::collection($digitalmanagerIntegrations),
                                        'projects'     => ProjectsSelectResource::collection($projects),
                                    ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param $id
     * @return HotZappResource
     */
    public function show($id)
    {

        $digitalmanagerIntegrationModel = new DigitalmanagerIntegration();
        $digitalmanagerIntegration      = $digitalmanagerIntegrationModel->find(current(Hashids::decode($id)));

        return new DigitalmanagerResource($digitalmanagerIntegration);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        try {
            $data                           = $request->all();
            $digitalmanagerIntegrationModel = new DigitalmanagerIntegration();

            $projectId = current(Hashids::decode($data['project_id']));
            if (!empty($projectId)) {
                $integration = $digitalmanagerIntegrationModel->where('project_id', $projectId)->first();
                if ($integration) {
                    return response()->json([
                                                'message' => 'Projeto já integrado',
                                            ], 400);
                }
                if (empty($data['boleto_generated'])) {
                    $data['boleto_generated'] = 0;
                }
                if (empty($data['boleto_paid'])) {
                    $data['boleto_paid'] = 0;
                }
                if (empty($data['credit_card_paid'])) {
                    $data['credit_card_paid'] = 0;
                }
                if (empty($data['credit_card_refused'])) {
                    $data['credit_card_refused'] = 0;
                }
                if (empty($data['abandoned_cart'])) {
                    $data['abandoned_cart'] = 0;
                }

                $integrationCreated = $digitalmanagerIntegrationModel->create([
                                                                           'api_token'           => $data['api_token'],
                                                                           'url'                 => $data['url'],
                                                                           'billet_generated'    => $data['boleto_generated'],
                                                                           'billet_paid'         => $data['boleto_paid'],
                                                                           'credit_card_refused' => $data['credit_card_refused'],
                                                                           'credit_card_paid'    => $data['credit_card_paid'],
                                                                           'abandoned_cart'      => $data['abandoned_cart'],
                                                                           'project_id'          => $projectId,
                                                                           'user_id'             => auth()->user()->account_owner_id,
                                                                       ]);

                if ($integrationCreated) {
                    return response()->json([
                                                'message' => 'Integração criada com sucesso!',
                                            ], 200);
                } else {

                    return response()->json([
                                                'message' => 'Ocorreu um erro ao realizar a integração',
                                            ], 400);
                }
            } else {

                return response()->json([
                                            'message' => 'Ocorreu um erro ao realizar a integração',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao realizar integração  DigitalManagerController - store');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao realizar a integração',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        try {
            if (!empty($id)) {
                $digitalmanagerIntegrationModel = new DigitalmanagerIntegration();
                $projectService          = new ProjectService();

                $projects = $projectService->getMyProjects();

                $projectId   = current(Hashids::decode($id));
                $integration = $digitalmanagerIntegrationModel->where('project_id', $projectId)->first();

                if ($integration) {
                    return response()->json(['projects' => $projects, 'integration' => $integration]);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                            ], 400);
                }
            } else {

                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar Integração HotZapp (DigitalManagerController - edit)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $digitalmanagerIntegrationModel = new DigitalmanagerIntegration();
        $data                           = $request->all();
        $integrationId                  = current(Hashids::decode($id));
        $digitalmanagerIntegration             = $digitalmanagerIntegrationModel->find($integrationId);
        if (empty($data['boleto_generated'])) {
            $data['boleto_generated'] = 0;
        }
        if (empty($data['boleto_paid'])) {
            $data['boleto_paid'] = 0;
        }
        if (empty($data['credit_card_paid'])) {
            $data['credit_card_paid'] = 0;
        }
        if (empty($data['credit_card_refused'])) {
            $data['credit_card_refused'] = 0;
        }
        if (empty($data['abandoned_cart'])) {
            $data['abandoned_cart'] = 0;
        }
        if (empty($data['abandoned_cart'])) {
            $data['abandoned_cart'] = 0;
        }

        $integrationUpdated = $digitalmanagerIntegration->update([
                                                              'api_token'           => $data['api_token'],
                                                              'url'                 => $data['url'],
                                                              'billet_generated'    => $data['boleto_generated'],
                                                              'billet_paid'         => $data['boleto_paid'],
                                                              'credit_card_refused' => $data['credit_card_refused'],
                                                              'credit_card_paid'    => $data['credit_card_paid'],
                                                              'abandoned_cart'      => $data['abandoned_cart'],
                                                          ]);
        if ($integrationUpdated) {
            return response()->json([
                                        'message' => 'Integração atualizada com sucesso!',
                                    ], 200);
        }

        return response()->json([
                                    'message' => 'Ocorreu um erro ao atualizar a integração',
                                ], 400);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $integrationId                  = current(Hashids::decode($id));
            $digitalmanagerIntegrationModel = new DigitalmanagerIntegration();
            $integration                    = $digitalmanagerIntegrationModel->find($integrationId);
            $integrationDeleted             = $integration->delete();
            if ($integrationDeleted) {
                return response()->json([
                                            'message' => 'Integração Removida com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Erro ao tentar remover Integração',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar remover Integração Digitalmanager (DigitalmanagerController - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao tentar remover, tente novamente mais tarde!',
                                    ], 400);
        }
    }
}
