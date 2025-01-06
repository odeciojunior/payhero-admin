<?php

namespace Modules\Smartfunnel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\SmartfunnelIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ProjectService;
use Modules\Smartfunnel\Transformers\SmartfunnelResource;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class SmartfunnelApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            activity()->on((new SmartfunnelIntegration()))->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos as integrações Smart Funnel');

            $smartfunnelIntegrations = SmartfunnelIntegration::with(['project', 'project.usersProjects'])
            ->whereHas(
                'project.usersProjects',
                function ($query) {
                    $query
                    ->where('company_id', auth()->user()->company_default)
                    ->where('user_id', auth()->user()->getAccountOwnerId());
                }
            )->get();

            $projects = collect();
            $userProjects = UserProject::where([[
                'user_id', $user->getAccountOwnerId()],[
                'company_id', $user->company_default
            ]])->orderBy('id', 'desc')->get();

            if ($userProjects->count() > 0) {
                foreach ($userProjects as $userProject) {
                    $project = $userProject
                        ->project()
                        ->leftjoin('domains',
                            function ($join) {
                                $join->on('domains.project_id', '=', 'projects.id')
                                    ->where('domains.status', 3)
                                    ->whereNull('domains.deleted_at');
                            }
                        )
                        ->where('projects.status', Project::STATUS_ACTIVE)
                        ->first();
                    if (!empty($project)) {
                        $projects->add($userProject->project);
                    }
                }
            }
            return response()->json([
                "integrations" => SmartfunnelResource::collection($smartfunnelIntegrations),
                "projects" => ProjectsSelectResource::collection($projects),
            ]);
        } catch (Exception $e) {
            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    /**
     * @param $id
     * @return SmartfunnelResource
     */
    public function show($id)
    {
        try {
            $smartfunnelIntegrationModel = new SmartfunnelIntegration();
            $smartfunnelIntegration = $smartfunnelIntegrationModel->find(current(Hashids::decode($id)));

            activity()
                ->on($smartfunnelIntegrationModel)
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = current(Hashids::decode($id));
                })
                ->log(
                    "Visualizou tela editar configurações de integração projeto " .
                        $smartfunnelIntegration->project->name .
                        " com Smart Funnel"
                );

            return new SmartfunnelResource($smartfunnelIntegration);
        } catch (Exception $e) {
            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $smartfunnelIntegrationModel = new SmartfunnelIntegration();

            $projectId = current(Hashids::decode($data["project_id"]));
            if (!empty($projectId)) {
                $integration = $smartfunnelIntegrationModel->where("project_id", $projectId)->first();
                if ($integration) {
                    return response()->json(
                        [
                            "message" => "Projeto já integrado",
                        ],
                        400
                    );
                }
                if (empty($data["api_url"])) {
                    return response()->json(["message" => "URl API é obrigatório!"], 400);
                }
                if (!filter_var($data["api_url"], FILTER_VALIDATE_URL)) {
                    return response()->json(["message" => "URL API inválido!"], 400);
                }

                $integrationCreated = $smartfunnelIntegrationModel->create([
                    'api_url'             => $data['api_url'],
                    'project_id'          => $projectId,
                    'user_id'             => auth()->user()->getAccountOwnerId(),
                ]);

                if ($integrationCreated) {
                    return response()->json(
                        [
                            "message" => "Integração criada com sucesso!",
                        ],
                        200
                    );
                } else {
                    return response()->json(
                        [
                            "message" => "Ocorreu um erro ao realizar a integração",
                        ],
                        400
                    );
                }
            } else {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro ao realizar a integração",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            Log::warning("Erro ao realizar integração  SmartfunnelController - store");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao realizar a integração",
                ],
                400
            );
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
                $SmartfunnelIntegrationModel = new SmartfunnelIntegration();
                $projectService = new ProjectService();

                activity()
                    ->on($SmartfunnelIntegrationModel)
                    ->tap(function (Activity $activity) use ($id) {
                        $activity->log_name = "visualization";
                        $activity->subject_id = current(Hashids::decode($id));
                    })
                    ->log("Visualizou tela editar configurações da integração Smart Funnel");

                $projects = $projectService->getMyProjects();

                $projectId = current(Hashids::decode($id));
                $integration = $SmartfunnelIntegrationModel->where("project_id", $projectId)->first();

                if ($integration) {
                    return response()->json(["projects" => $projects, "integration" => $integration]);
                } else {
                    return response()->json(
                        [
                            "message" => __('messages.unexpected_error'),
                        ],
                        400
                    );
                }
            } else {
                return response()->json(
                    [
                        "message" => __('messages.unexpected_error'),
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            Log::warning("Erro ao tentar acessar tela editar Integração Smartfunnel (SmartfunnelController - edit)");
            report($e);

            return response()->json(
                [
                    "message" => __('messages.unexpected_error'),
                ],
                400
            );
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
            $smartfunnelIntegrationModel = new SmartfunnelIntegration();
            $data = $request->all();
            $integrationId = current(Hashids::decode($id));
            $smartfunnelIntegration = $smartfunnelIntegrationModel->find($integrationId);
            $messageError = "";

            if (empty($data["api_url"])) {
                return response()->json(["message" => "URl API é obrigatório!"], 400);
            }
            if (!filter_var($data["api_url"], FILTER_VALIDATE_URL)) {
                return response()->json(["message" => "URL API inválido!"], 400);
            }

            $integrationUpdated = $smartfunnelIntegration->update(["api_url" => $data["api_url"]]);

            if ($integrationUpdated) {
                return response()->json(
                    [
                        "message" => "Integração atualizada com sucesso!",
                    ],
                    200
                );
            }

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao atualizar a integração",
                ],
                400
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao atualizar a integração",
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
            $integrationId = current(Hashids::decode($id));
            $smartfunnelIntegrationModel = new SmartfunnelIntegration();
            $integration = $smartfunnelIntegrationModel->find($integrationId);
            if (empty($integration)) {
                return response()->json(
                    [
                        "message" => "Erro ao tentar remover Integração",
                    ],
                    400
                );
            } else {
                $integrationDeleted = $integration->delete();
                if ($integrationDeleted) {
                    return response()->json(
                        [
                            "message" => "Integração Removida com sucesso!",
                        ],
                        200
                    );
                }

                return response()->json(
                    [
                        "message" => "Erro ao tentar remover Integração",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            Log::warning("Erro ao tentar remover Integração Smart Funnel (SmartfunnelController - destroy)");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao tentar remover, tente novamente mais tarde!",
                ],
                400
            );
        }
    }
}
