<?php

namespace Modules\Nuvemshop\Http\Controllers;

use App\Jobs\ImportNuvemshopProductsStore;
use App\Jobs\ImportNuvemshopTrackingCodesJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\CheckoutConfig;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\NuvemshopIntegration;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\UserProject;
use Modules\Core\Events\ImportNuvemshopProductsEvent;
use Modules\Core\Services\Nuvemshop\NuvemshopAPI;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\TaskService;
use Modules\Nuvemshop\Transformers\NuvemshopResource;
use Spatie\Activitylog\Models\Activity;

class NuvemshopApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $projectModel = new Project();
            $nuvemshopIntegrationModel = new NuvemshopIntegration();

            activity()
                ->on($nuvemshopIntegrationModel)
                ->tap(function (Activity $activity) {
                    $activity->log_name = "visualization";
                })
                ->log("Visualizou tela todos as integrações com o nuvemshop");

            $nuvemshopIntegrations = $nuvemshopIntegrationModel
                ->join("checkout_configs as cc", "cc.project_id", "=", "nuvemshop_integrations.project_id")
                ->where("cc.company_id", hashids_decode($request->company))
                ->where("user_id", auth()->user()->account_owner_id)
                ->get();

            $projects = [];

            foreach ($nuvemshopIntegrations as $nuvemshopIntegration) {
                $project = $projectModel
                    ->where("id", $nuvemshopIntegration->project_id)
                    ->where("status", $projectModel->present()->getStatus("active"))
                    ->first();

                if (!empty($project)) {
                    $projects[] = $project;
                }
            }

            return NuvemshopResource::collection(collect($projects));
        } catch (Exception $e) {
            return response()->json(["message" => "Ocorreu algum erro"], Response::HTTP_BAD_REQUEST);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            $company = Company::find(hashids_decode($data["company"]));
            if (empty($company)) {
                return response()->json(
                    ["message" => "A empresa precisa estar aprovada transacionar para realizar a integração!"],
                    400,
                );
            }

            $urlStore = $data["url_store"] . ".lojavirtualnuvem.com.br";
            $existingIntegration = NuvemshopIntegration::where("url_store", $urlStore)->first();
            if ($existingIntegration) {
                $message =
                    $existingIntegration->status == ShopifyIntegration::STATUS_PENDING
                        ? "Integração em andamento"
                        : "Projeto já integrado";
                return response()->json(["message" => $message], 400);
            }

            $shopName = ucfirst(strtolower($data["url_store"]));
            $project = Project::create([
                "name" => $shopName,
                "status" => Project::STATUS_ACTIVE,
                "visibility" => "private",
                "percentage_affiliates" => "0",
                "description" => $shopName,
                "url_page" => "https://" . $urlStore,
                "automatic_affiliation" => false,
                "notazz_configs" => json_encode(["cost_currency_type" => 1, "update_cost_shopify" => 1]),
            ]);
            if (empty($project)) {
                return response()->json(["message" => "Problema ao criar integração, tente novamente mais tarde"], 400);
            }

            $bankAccount = $company->getDefaultBankAccount();
            $checkoutConfig = CheckoutConfig::create([
                "company_id" => $company->id,
                "project_id" => $project->id,
                "pix_enabled" => !!($bankAccount && $bankAccount->transfer_type == "PIX"),
            ]);
            if (empty($checkoutConfig)) {
                $project->delete();
                return response()->json(["message" => "Problema ao criar integração, tente novamente mais tarde"], 400);
            }

            $shipping = Shipping::create([
                "project_id" => $project->id,
                "name" => "Frete gratis",
                "information" => "de 15 até 30 dias",
                "value" => "0,00",
                "type" => "static",
                "type_enum" => Shipping::TYPE_STATIC_ENUM,
                "status" => "1",
                "pre_selected" => "1",
                "apply_on_plans" => '["all"]',
                "not_apply_on_plans" => "[]",
            ]);
            if (empty($shipping)) {
                $project->delete();
                return response()->json(["message" => "Problema ao criar integração, tente novamente mais tarde"], 400);
            }

            $nuvemshopIntegration = NuvemshopIntegration::create([
                "user_id" => auth()->user()->account_owner_id,
                "project_id" => $project->id,
                "url_store" => $urlStore,
                "status" => NuvemshopIntegration::STATUS_PENDING,
            ]);
            if (empty($nuvemshopIntegration)) {
                $shipping->delete();
                $project->delete();
                return response()->json(["message" => "Problema ao criar integração, tente novamente mais tarde"], 400);
            }

            $userProjectCreated = UserProject::create([
                "user_id" => auth()->user()->account_owner_id,
                "project_id" => $project->id,
                "company_id" => $company->id,
                "type" => "producer",
                "type_enum" => UserProject::TYPE_PRODUCER_ENUM,
                "shipment_responsible" => true,
                "access_permission" => true,
                "edit_permission" => true,
                "status" => "active",
                "status_flag" => UserProject::STATUS_FLAG_ACTIVE,
            ]);
            if (empty($userProjectCreated)) {
                $nuvemshopIntegration->delete();
                $shipping->delete();
                $project->delete();
                return response()->json(["message" => "Problema ao criar integração, tente novamente mais tarde"], 400);
            }

            (new ProjectNotificationService())->createProjectNotificationDefault($project->id);
            (new ProjectService())->createUpsellConfig($project->id);
            TaskService::setCompletedTask(auth()->user(), Task::find(Task::TASK_CREATE_FIRST_STORE));

            $integrationId = hashids_encode($nuvemshopIntegration->id);
            $returnUrl = "https://" . $urlStore . "/admin/apps/" . env("NUVEMSHOP_CLIENT_ID") . "/authorize";

            $response = [
                "url" => $returnUrl,
                "integration_id" => $integrationId,
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Problema ao criar integração, tente novamente mais tarde"], 400);
        }
    }

    public function finalizeIntegration(): JsonResponse
    {
        try {
            $data = request()->all();

            if (empty($data["integration_id"])) {
                return response()->json(["message" => "Integração não encontrada"], 400);
            }

            $integrationId = hashids_decode($data["integration_id"]);

            $nuvemshopIntegration = NuvemshopIntegration::find($integrationId);

            if (empty($nuvemshopIntegration)) {
                return response()->json(["message" => "Integração não encontrada"], 400);
            }

            if (empty($data["token"])) {
                return response()->json(["message" => "Token não encontrado"], 400);
            }

            $response = NuvemshopAPI::authenticate($data["token"]);

            if (empty($response["access_token"])) {
                return response()->json(["message" => "Token inválido"], 400);
            }

            Project::where("id", $nuvemshopIntegration->project_id)->update([
                "nuvemshop_id" => $response["user_id"],
                "status" => Project::STATUS_ACTIVE,
            ]);

            $nuvemshopIntegration->token = $response["access_token"];
            $nuvemshopIntegration->store_id = $response["user_id"];
            $nuvemshopIntegration->status = NuvemshopIntegration::STATUS_ACTIVE;
            $nuvemshopIntegration->save();

            event(new ImportNuvemshopProductsEvent($nuvemshopIntegration));

            return response()->json(["message" => "Integração finalizada com sucesso"], 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Problema ao finalizar integração, tente novamente mais tarde"], 400);
        }
    }

    public function syncProducts(Request $request): JsonResponse
    {
        try {
            $projectId = hashids_decode($request->project_id);
            $nuvemshopIntegration = NuvemshopIntegration::where("project_id", $projectId)->first();

            if (empty($nuvemshopIntegration)) {
                return response()->json(["message" => "Integração não encontrada"], 400);
            }

            event(new ImportNuvemshopProductsEvent($nuvemshopIntegration));

            return response()->json(["message" => "Os produtos estão sendo sincronizados."], 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Problema ao sincronizar produtos, tente novamente mais tarde"], 400);
        }
    }

    public function syncTrackings(Request $request): JsonResponse
    {
        try {
            $projectId = hashids_decode($request->project_id);
            $nuvemshopIntegration = NuvemshopIntegration::where("project_id", $projectId)->first();

            if (empty($nuvemshopIntegration)) {
                return response()->json(["message" => "Integração não encontrada"], 400);
            }

            ImportNuvemshopTrackingCodesJob::dispatch($nuvemshopIntegration);

            return response()->json(["message" => "Os códigos de rastreio estão sendo sincronizados."], 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(
                ["message" => "Problema ao sincronizar códigos de rastreio, tente novamente mais tarde"],
                400,
            );
        }
    }
}
