<?php

namespace Modules\GeradorRastreio\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
use Google\Service\ApigeeRegistry\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\WebhookTracking;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ProjectService;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Modules\GeradorRastreio\Transformers\WebhookTrackingsResource;

class GeradorRastreioApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            activity()->on((new WebhookTracking()))->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos as integrações GR Soluções');

            $WebhookTracking = WebhookTracking::with(['project', 'project.usersProjects'])
            ->whereHas(
                'project.usersProjects',
                function ($query) {
                    $query
                    ->where('company_id', auth()->user()->company_default)
                    ->where('user_id', auth()->user()->getAccountOwnerId());
                }
            )->get();

            $projects     = collect();
            $userProjects = UserProject::where([[
                'user_id', auth()->user()->getAccountOwnerId()],[
                'company_id', auth()->user()->company_default
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
                "integrations" => WebhookTrackingsResource::collection($WebhookTracking),
                "projects" => ProjectsSelectResource::collection($projects),
            ]);
        } catch (Exception $e) {
            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    /**
     * @param $id
     * @return ReportanaResource
     */
    public function show($id)
    {
        try {
            $WebhookTrackingsModel = new WebhookTracking();
            $WebhookTracking = $WebhookTrackingsModel->find(current(Hashids::decode($id)));

            activity()
                ->on($WebhookTrackingsModel)
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = current(Hashids::decode($id));
                })
                ->log(
                    "Visualizou tela editar configurações de integração projeto " .
                        $WebhookTracking->project->name .
                        " com Gerador de Rastreio"
                );

            return new WebhookTrackingsResource($WebhookTracking);
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
            $WebhookTrackingsModel = new WebhookTracking();

            $projectId = current(Hashids::decode($data["project_id"]));
            if (!empty($projectId)) {
                if (empty($data["clientid"])) {
                    return response()->json(["message" => "CLIENT ID é obrigatório!"], 400);
                }

                $token = new ApiToken();
                $tokenid = $token->newQuery()
                    ->where('description', 'GR_Solucoes')
                    ->where('user_id', auth()->user()->account_owner_id)
                    ->where('deleted_at', null)
                    ->first();

                $integrationCreated = $WebhookTrackingsModel->firstOrCreate([
                    "user_id" => auth()->user()->account_owner_id,
                    "project_id" => $projectId,
                    "token_id" => $tokenid->id,
                    "clientid" => $data["clientid"],
                    "webhook_url" => 'https://geradorderastreio.com/webhook/azcend',
                    "credit_flag" => $data["credit_flag"] ?? 0,
                    "pix_flag" => $data["pix_flag"] ?? 0,
                    "billet_flag" => $data["billet_flag"] ?? 0,
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
                            "message" => "Ocorreu um erro ao realizar a integração ",
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
            Log::warning("Erro ao realizar integração  GeradorRastreioController - store");
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
                $WebhookTrackingsModel = new WebhookTracking();
                $projectService = new ProjectService();

                activity()
                    ->on($WebhookTrackingsModel)
                    ->tap(function (Activity $activity) use ($id) {
                        $activity->log_name = "visualization";
                        $activity->subject_id = current(Hashids::decode($id));
                    })
                    ->log("Visualizou tela editar configurações da integração GR Soluções");

                $projects = $projectService->getMyProjects();

                $projectId = current(Hashids::decode($id));
                $integration = $WebhookTrackingsModel->where("project_id", $projectId)->first();

                if ($integration) {
                    return response()->json(["projects" => $projects, "integration" => $integration]);
                } else {
                    return response()->json(
                        [
                            "message" => "Ocorreu um erro, tente novamente mais tarde!",
                        ],
                        400
                    );
                }
            } else {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro, tente novamente mais tarde!",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            Log::warning("Erro ao tentar acessar tela editar Integração GR Soluções (GeradorRastreioController - edit)");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro, tente novamente mais tarde!",
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
            $WebhookTrackingsModel = new WebhookTracking();
            $data = $request->all();
            $integrationId = current(Hashids::decode($id));
            $WebhookTracking = $WebhookTrackingsModel->find($integrationId);
            $messageError = "";
            if (empty($data["clientid"])) {
                return response()->json(["message" => "CLIENT ID é obrigatório!"], 400);
            }

            $integrationUpdated = $WebhookTracking->update([
                "clientid" => $data["clientid"],
                "credit_flag" => $data["credit_flag_edit"] ?? 0,
                "pix_flag" => $data["pix_flag_edit"] ?? 0,
                "billet_flag" => $data["billet_flag_edit"] ?? 0,
            ]);

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
                    "message" => "Ocorreu um erro ao tentar atualizar a integração", $e
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
            $WebhookTrackingsnModel = new WebhookTracking();
            $integration = $WebhookTrackingsnModel->find($integrationId);
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
            Log::warning("Erro ao tentar remover Integração Reportana (ReportanaController - destroy)");
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
