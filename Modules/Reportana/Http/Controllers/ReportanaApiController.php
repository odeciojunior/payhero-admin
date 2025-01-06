<?php

namespace Modules\Reportana\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ProjectService;
use Modules\Reportana\Transformers\ReportanaResource;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class ReportanaApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            activity()->on((new ReportanaIntegration()))->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos as integrações Reportana');

            $reportanaIntegrations = ReportanaIntegration::with(['project', 'project.usersProjects'])
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
                "integrations" => ReportanaResource::collection($reportanaIntegrations),
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
            $reportanaIntegrationModel = new ReportanaIntegration();
            $reportanaIntegration = $reportanaIntegrationModel->find(current(Hashids::decode($id)));

            activity()
                ->on($reportanaIntegrationModel)
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = current(Hashids::decode($id));
                })
                ->log(
                    "Visualizou tela editar configurações de integração projeto " .
                        $reportanaIntegration->project->name .
                        " com Reportana"
                );

            return new ReportanaResource($reportanaIntegration);
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
            $reportanaIntegrationModel = new ReportanaIntegration();

            $projectId = current(Hashids::decode($data["project_id"]));
            if (!empty($projectId)) {
                if (empty($data["client_id"])) {
                    return response()->json(["message" => "CLIENT ID é obrigatório!"], 400);
                }
                if (empty($data["client_secret"])) {
                    return response()->json(["message" => "CLIENT SECRET é obrigatório!"], 400);
                }
                if (empty($data["boleto_generated"])) {
                    $data["boleto_generated"] = 0;
                }
                if (empty($data["boleto_paid"])) {
                    $data["boleto_paid"] = 0;
                }
                if (empty($data["boleto_expired"])) {
                    $data["boleto_expired"] = 0;
                }
                if (empty($data["credit_card_paid"])) {
                    $data["credit_card_paid"] = 0;
                }
                if (empty($data["credit_card_refused"])) {
                    $data["credit_card_refused"] = 0;
                }
                if (empty($data["pix_generated"])) {
                    $data["pix_generated"] = 0;
                }
                if (empty($data["pix_paid"])) {
                    $data["pix_paid"] = 0;
                }
                if (empty($data["pix_expired"])) {
                    $data["pix_expired"] = 0;
                }
                if (empty($data["abandoned_cart"])) {
                    $data["abandoned_cart"] = 0;
                }

                $integrationCreated = $reportanaIntegrationModel->firstOrCreate([
                    "client_id" => $data["client_id"],
                    "client_secret" => $data["client_secret"],
                    "billet_generated" => $data["boleto_generated"],
                    "billet_paid" => $data["boleto_paid"],
                    "billet_expired" => $data["boleto_expired"],
                    "credit_card_refused" => $data["credit_card_refused"],
                    "credit_card_paid" => $data["credit_card_paid"],
                    "pix_generated" => $data["pix_generated"],
                    "pix_paid" => $data["pix_paid"],
                    "pix_expired" => $data["pix_expired"],
                    "abandoned_cart" => $data["abandoned_cart"],
                    "project_id" => $projectId,
                    "user_id" => auth()->user()->account_owner_id,
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
            Log::warning("Erro ao realizar integração  ReportanaController - store");
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
                $reportanaIntegrationModel = new ReportanaIntegration();
                $projectService = new ProjectService();

                activity()
                    ->on($reportanaIntegrationModel)
                    ->tap(function (Activity $activity) use ($id) {
                        $activity->log_name = "visualization";
                        $activity->subject_id = current(Hashids::decode($id));
                    })
                    ->log("Visualizou tela editar configurações da integração Reportana");

                $projects = $projectService->getMyProjects();

                $projectId = current(Hashids::decode($id));
                $integration = $reportanaIntegrationModel->where("project_id", $projectId)->first();

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
            Log::warning("Erro ao tentar acessar tela editar Integração Reportana (ReportanaController - edit)");
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
            $reportanaIntegrationModel = new ReportanaIntegration();
            $data = $request->all();
            $integrationId = current(Hashids::decode($id));
            $reportanaIntegration = $reportanaIntegrationModel->find($integrationId);
            $messageError = "";
            if (empty($data["client_id"])) {
                return response()->json(["message" => "CLIENT ID é obrigatório!"], 400);
            }
            if (empty($data["client_secret"])) {
                return response()->json(["message" => "CLIENT SECRET é obrigatório!"], 400);
            }
            if (empty($data["boleto_generated"])) {
                $data["boleto_generated"] = 0;
            }
            if (empty($data["boleto_paid"])) {
                $data["boleto_paid"] = 0;
            }
            if (empty($data["boleto_expired"])) {
                $data["boleto_expired"] = 0;
            }
            if (empty($data["credit_card_paid"])) {
                $data["credit_card_paid"] = 0;
            }
            if (empty($data["credit_card_refused"])) {
                $data["credit_card_refused"] = 0;
            }
            if (empty($data["pix_generated"])) {
                $data["pix_generated"] = 0;
            }
            if (empty($data["pix_paid"])) {
                $data["pix_paid"] = 0;
            }
            if (empty($data["pix_expired"])) {
                $data["pix_expired"] = 0;
            }
            if (empty($data["abandoned_cart"])) {
                $data["abandoned_cart"] = 0;
            }

            $integrationUpdated = $reportanaIntegration->update([
                "client_id" => $data["client_id"],
                "client_secret" => $data["client_secret"],
                "billet_generated" => $data["boleto_generated"],
                "billet_paid" => $data["boleto_paid"],
                "billet_expired" => $data["boleto_expired"],
                "credit_card_refused" => $data["credit_card_refused"],
                "credit_card_paid" => $data["credit_card_paid"],
                "pix_generated" => $data["pix_generated"],
                "pix_paid" => $data["pix_paid"],
                "pix_expired" => $data["pix_expired"],
                "abandoned_cart" => $data["abandoned_cart"],
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
            $reportanaIntegrationModel = new ReportanaIntegration();
            $integration = $reportanaIntegrationModel->find($integrationId);
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
