<?php

namespace Modules\Utmify\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UtmifyIntegration;
use Modules\Core\Services\ProjectService;
use Modules\Projects\Transformers\ProjectsSelectResource;
use Modules\Utmify\Transformers\UtmifyIntegrationResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class UtmifyApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->logActivity("visualization", "Visualizou tela todos as integrações Utmify");

            $userId = auth()
                ->user()
                ->getAccountOwnerId();

            $projectIds = DB::table("users_projects as up")
                ->join("projects as p", "p.id", "=", "up.project_id")
                ->leftJoin("domains as d", "d.project_id", "=", "p.id")
                ->where("up.user_id", $userId)
                ->where("d.status", Domain::STATUS_APPROVED)
                ->where("p.status", Project::STATUS_ACTIVE)
                ->whereNull("d.deleted_at")
                ->pluck("p.id");

            $utmifyIntegrations = UtmifyIntegration::with(["project"])
                ->whereIn("project_id", $projectIds)
                ->get();

            $projects = Project::whereIn("id", $projectIds)->get();

            return response()->json([
                "integrations" => UtmifyIntegrationResource::collection($utmifyIntegrations),
                "projects" => ProjectsSelectResource::collection($projects),
            ]);
        } catch (Exception $e) {
            return $this->handleException();
        }
    }

    public function show($id)
    {
        try {
            $utmifyIntegration = UtmifyIntegration::with("project")->find($this->decodeId($id));

            $this->logActivity(
                "visualization",
                "Visualizou tela editar configurações de integração projeto {$utmifyIntegration->project->name} com Utmify",
                $id,
            );

            return new UtmifyIntegrationResource($utmifyIntegration);
        } catch (Exception $e) {
            return $this->handleException();
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                "project_id" => "required",
                "token" => "required",
            ]);

            $projectId = $this->decodeId($data["project_id"]);

            $ownerId = auth()
                ->user()
                ->getAccountOwnerId();

            if ($projectId) {
                $integrationCreated = UtmifyIntegration::firstOrCreate([
                    "user_id" => $ownerId,
                    "project_id" => $projectId,
                    "token" => $data["token"],
                ]);

                return $integrationCreated
                    ? response()->json(["message" => "Integração criada com sucesso!"], 200)
                    : response()->json(["message" => "Ocorreu um erro ao realizar a integração"], 400);
            }

            return response()->json(["message" => "Ocorreu um erro ao realizar a integração"], 400);
        } catch (Exception $e) {
            return $this->handleException($e, "Erro ao realizar integração UtmifyApiController - store");
        }
    }

    public function edit($id)
    {
        try {
            $projectId = $this->decodeId($id);

            if ($projectId) {
                $projectService = new ProjectService();

                $this->logActivity("visualization", "Visualizou tela editar configurações da integração Utmify", $id);

                $projects = $projectService->getMyProjects();
                $integration = UtmifyIntegration::where("project_id", $projectId)->first();

                return $integration
                    ? response()->json(["projects" => $projects, "integration" => $integration])
                    : response()->json(["message" => __('messages.unexpected_error')], 400);
            }

            return response()->json(["message" => __('messages.unexpected_error')], 400);
        } catch (Exception $e) {
            return $this->handleException(
                $e,
                "Erro ao tentar acessar tela editar Integração Utmify (UtmifyApiController - edit)",
            );
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                "token" => "required",
            ]);

            $integrationId = $this->decodeId($id);
            $utmifyIntegration = UtmifyIntegration::find($integrationId);

            $integrationUpdated = $utmifyIntegration->update(["token" => $data["token"]]);

            return $integrationUpdated
                ? response()->json(["message" => "Integração atualizada com sucesso!"], 200)
                : response()->json(["message" => "Ocorreu um erro ao atualizar a integração"], 400);
        } catch (Exception $e) {
            return $this->handleException();
        }
    }

    public function destroy($id)
    {
        try {
            $integrationId = $this->decodeId($id);
            $integration = UtmifyIntegration::find($integrationId);

            if ($integration) {
                $integrationDeleted = $integration->delete();

                return $integrationDeleted
                    ? response()->json(["message" => "Integração Removida com sucesso!"], 200)
                    : response()->json(["message" => "Erro ao tentar remover Integração"], 400);
            }

            return response()->json(["message" => "Erro ao tentar remover Integração"], 400);
        } catch (Exception $e) {
            return $this->handleException(
                $e,
                "Erro ao tentar remover Integração Reportana (ReportanaController - destroy)",
            );
        }
    }

    private function decodeId($id)
    {
        return current(Hashids::decode($id));
    }

    private function logActivity($logName, $message, $id = null)
    {
        activity()
            ->on(new UtmifyIntegration())
            ->tap(function (Activity $activity) use ($logName, $id) {
                $activity->log_name = $logName;
                if ($id) {
                    $activity->subject_id = $this->decodeId($id);
                }
            })
            ->log($message);
    }

    private function handleException(Exception $e = null, $logMessage = null)
    {
        if ($logMessage) {
            Log::warning($logMessage);
        }
        if ($e) {
            report($e);
        }
        return response()->json(["message" => __('messages.unexpected_error')], 400);
    }
}
