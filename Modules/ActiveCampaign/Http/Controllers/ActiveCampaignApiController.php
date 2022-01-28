<?php

namespace Modules\ActiveCampaign\Http\Controllers;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\ActiveCampaign\Transformers\ActivecampaignResource;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ActiveCampaignService;
use Modules\Core\Services\ProjectService;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class ActiveCampaignApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $activecampaignIntegration = new ActivecampaignIntegration();
            $userProjectModel          = new UserProject();

            activity()->on($activecampaignIntegration)->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todas as integrações do ActiveCampaign');

            $activecampaignIntegrations = $activecampaignIntegration->where('user_id', $user->account_owner_id)->with('project')
                                                                    ->get();

            $projects     = collect();
            $userProjects = $userProjectModel->where('user_id', $user->account_owner_id)->with('project')->get();
            if ($userProjects->count() > 0) {
                foreach ($userProjects as $userProject) {
                    if (!empty($userProject->project)) {
                        $projects->add($userProject->project);
                    }
                }
            }
            return response()->json(['integrations' => ActivecampaignResource::collection($activecampaignIntegrations),
                'projects' => ProjectsSelectResource::collection($projects)
            ]);
        }
        catch(Exception $e){
            report($e);
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param $id
     * @return ActivecampaignResource
     */
    public function show($id)
    {
        $activecampaignIntegrationModel = new ActivecampaignIntegration();

        activity()->on($activecampaignIntegrationModel)->tap(function(Activity $activity) use ($id) {
            $activity->log_name   = 'visualization';
            $activity->subject_id = current(Hashids::decode($id));
        })->log('Visualizou tela configuração ActiveCampaign');

        $activecampaignIntegration = $activecampaignIntegrationModel->find(current(Hashids::decode($id)));

        if (Gate::denies('show', [$activecampaignIntegration])) {
            return response()->json([
                    'message' => 'Sem permissão',
                ],Response::HTTP_FORBIDDEN
            );
        }

        return new ActivecampaignResource($activecampaignIntegration);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        try {
            $data                           = $request->all();
            $activecampaignIntegrationModel = new ActivecampaignIntegration();

            $projectId = current(Hashids::decode($data['project_id']));
            if (!empty($projectId)) {
                $integration = $activecampaignIntegrationModel->where('project_id', $projectId)->first();
                if ($integration) {
                    return response()->json([
                                                'message' => 'Projeto já integrado',
                                            ], 400);
                }
                if (empty($data['api_url']) || empty($data['api_key'])) {
                    return response()->json([
                                                'message' => 'Ocorreu um erro ao realizar a integração. Campos obrigatórios não informados',
                                            ], 400);
                }

                $activecampaignService = new ActiveCampaignService();
                $activecampaignService->setAccess($data['api_url'], $data['api_key'], 1);
                $listTest = $activecampaignService->getLists();

                if (isset($listTest['lists'])) {
                    $integrationCreated = $activecampaignIntegrationModel->create([
                                                                                      'api_url'    => $data['api_url'],
                                                                                      'api_key'    => $data['api_key'],
                                                                                      'project_id' => $projectId,
                                                                                      'user_id'    => auth()->user()->id,
                                                                                  ]);
                    $activecampaignService->createCustomFieldsDefault($integrationCreated->id);
                } else {
                    return response()->json([
                                                'message' => 'Erro ao realizar a integração. API URL e/ou API Key inválido(s)',
                                            ], 400);
                }

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
            Log::warning('Erro ao realizar integração ActiveCampaignController - store');
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
                $activecampaignIntegrationModel = new ActivecampaignIntegration();
                $projectService                 = new ProjectService();

                activity()->on($activecampaignIntegrationModel)->tap(function(Activity $activity) use ($id) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })->log('Visualizou tela editar integração ActiveCampaign');

                $projects = $projectService->getMyProjects();

                $projectId   = current(Hashids::decode($id));
                $integration = $activecampaignIntegrationModel->where('project_id', $projectId)->first();

                if (Gate::denies('edit', [$integration])) {
                    return response()->json([
                            'message' => 'Sem permissão',
                        ],Response::HTTP_FORBIDDEN
                    );
                }

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
            Log::warning('Erro ao tentar acessar tela editar Integração ActiveCampaign (ActiveCampaignController - edit)');
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
        try {
            $activecampaignIntegrationModel = new ActivecampaignIntegration();
            $data                           = $request->all();
            $integrationId                  = current(Hashids::decode($id));
            $activecampaignIntegration      = $activecampaignIntegrationModel->find($integrationId);


            if (Gate::denies('update', [$activecampaignIntegration])) {
                return response()->json([
                        'message' => 'Sem permissão',
                    ],Response::HTTP_FORBIDDEN
                );
            }

            if (empty($data['api_url']) || empty($data['api_key'])) {
                return response()->json([
                                            'message' => 'Erro ao atualizar a integração. Campos obrigatórios não informados',
                                        ], 400);
            }

            $activecampaignService = new ActiveCampaignService();
            $activecampaignService->setAccess($data['api_url'], $data['api_key'], $integrationId);
            $listTest = $activecampaignService->getLists();

            if (isset($listTest['lists'])) {
                $integrationUpdated = $activecampaignIntegration->update([
                                                                             'api_url' => $data['api_url'],
                                                                             'api_key' => $data['api_key'],
                                                                         ]);
            } else {
                return response()->json([
                                            'message' => 'Erro ao atualizar a integração. API URL e/ou API Key inválido(s)',
                                        ], 400);
            }

            if ($integrationUpdated) {
                return response()->json([
                                            'message' => 'Integração atualizada com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Ocorreu um erro ao atualizar a integração',
                                    ], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao tentar atualizar a integração!',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $integrationId                  = current(Hashids::decode($id));
            $activecampaignIntegrationModel = new ActivecampaignIntegration();
            $integration                    = $activecampaignIntegrationModel->find($integrationId);

            if (Gate::denies('destroy', [$integration])) {
                return response()->json([
                        'message' => 'Sem permissão',
                    ],Response::HTTP_FORBIDDEN
                );
            }

            $integrationDeleted = $integration->delete();

            if ($integrationDeleted) {
                return response()->json([
                                            'message' => 'Integração Removida com sucesso!',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Erro ao tentar remover Integração',
                                    ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar remover Integração ActiveCampaign (ActiveCampaignController - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro ao tentar remover, tente novamente mais tarde!',
                                    ], 400);
        }
    }
}
