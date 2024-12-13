<?php

namespace Modules\Shopify\Http\Controllers;

use App\Jobs\ImportShopifyProductsStore;
use App\Jobs\ImportShopifyTrackingCodesJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\CheckoutConfig;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\Shopify\ShopService;
use Modules\Core\Services\ShopifyErrors;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\TaskService;
use Modules\Core\Transformers\CompaniesSelectResource;
use Modules\Shopify\Transformers\ShopifyResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

use function PHPUnit\Framework\isEmpty;

class ShopifyApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $projectModel = new Project();
            $shopifyIntegrationModel = new ShopifyIntegration();

            activity()
                ->on($shopifyIntegrationModel)
                ->tap(function (Activity $activity) {
                    $activity->log_name = "visualization";
                })
                ->log("Visualizou tela todos as integrações com o shopify");

            $shopifyIntegrations = $shopifyIntegrationModel
                ->join("checkout_configs as cc", "cc.project_id", "=", "shopify_integrations.project_id")
                ->where("cc.company_id", hashids_decode($request->company))
                ->where("user_id", auth()->user()->account_owner_id)
                ->get();

            $projects = [];

            foreach ($shopifyIntegrations as $shopifyIntegration) {
                $project = $projectModel
                    ->where("id", $shopifyIntegration->project_id)
                    ->where("status", $projectModel->present()->getStatus("active"))
                    ->first();

                if (!empty($project)) {
                    $projects[] = $project;
                }
            }

            return ShopifyResource::collection(collect($projects));
        } catch (Exception $e) {
            return response()->json(["message" => __('messages.unexpected_error')], Response::HTTP_BAD_REQUEST);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $dataRequest = $request->all();

            if (0 === preg_match('/^([a-zA-Z0-9_]{10,100})$/', $dataRequest["token"])) {
                return response()->json(["message" => "O token deve ter entre 10 e 100 letras e números!"], 400);
            }

            if (empty($dataRequest["company"])) {
                return response()->json(
                    [
                        "message" => "A empresa precisa estar aprovada transacionar para realizar a integração!",
                    ],
                    400,
                );
            }

            if (!auth()->user()->account_is_approved) {
                return response()->json(["message" => "Finalize seu cadastro para integrar com Shopify"], 400);
            }

            $dataRequest["url_store"] = str_replace("http://", "", $dataRequest["url_store"]);
            $dataRequest["url_store"] = str_replace("https://", "", $dataRequest["url_store"]);

            $shopifyIntegration = ShopifyIntegration::where("url_store", $dataRequest["url_store"] . ".myshopify.com")
                ->orWhere("token", $dataRequest["token"])
                ->first();

            if ($shopifyIntegration) {
                if ($shopifyIntegration->status == ShopifyIntegration::STATUS_PENDING) {
                    return response()->json(["message" => "Integração em andamento"], 400);
                }

                return response()->json(["message" => "Projeto já integrado"], 400);
            }

            try {
                $dataRequest["url_store"] = "http://" . $dataRequest["url_store"];
                $dataRequest["url_store"] = parse_url($dataRequest["url_store"], PHP_URL_HOST);

                $urlStore = str_replace(".myshopify.com", "", $dataRequest["url_store"]);

                $shopifyService = new ShopifyService($urlStore . ".myshopify.com", $dataRequest["token"]);
            } catch (Exception $e) {
                report($e);
                return response()->json(
                    [
                        "message" => (new ShopifyErrors())->FormatDataInvalidShopifyIntegration($e),
                    ],
                    400,
                );
            }

            $tokenPermissions = $shopifyService->verifyPermissions();
            if ($tokenPermissions["status"] == "error") {
                return response()->json(["message" => $tokenPermissions["message"]], 400);
            }

            $shopifyName = $shopifyService->getShopName();

            $company = Company::find(hashids_decode($dataRequest["company"]));

            $projectCreated = Project::create([
                "name" => $shopifyName,
                "status" => Project::STATUS_ACTIVE,
                "visibility" => "private",
                "percentage_affiliates" => "0",
                "description" => $shopifyName,
                "url_page" => "https://" . $shopifyService->getShopDomain(),
                "automatic_affiliation" => false,
                "shopify_id" => $shopifyService->getShopId(),
                "notazz_configs" => json_encode([
                    "cost_currency_type" => 1,
                    "update_cost_shopify" => 1,
                ]),
            ]);

            if (empty($projectCreated)) {
                return response()->json(["message" => "Problema ao criar integração, tente novamente mais tarde"], 400);
            }

            $bankAccount = $company->getDefaultBankAccount();

            $checkoutConfig = CheckoutConfig::create([
                "company_id" => $company->id,
                "project_id" => $projectCreated->id,
                "pix_enabled" => !!(!empty($bankAccount) && $bankAccount->transfer_type == "PIX"),
            ]);

            if (empty($checkoutConfig)) {
                $projectCreated->delete();
                return response()->json(["message" => "Problema ao criar integração, tente novamente mais tarde"], 400);
            }

            $shippingCreated = Shipping::create([
                "project_id" => $projectCreated->id,
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

            if (empty($shippingCreated)) {
                $projectCreated->delete();

                return response()->json(
                    [
                        "message" => "Problema ao criar integração, tente novamente mais tarde",
                    ],
                    400,
                );
            }

            $discountCoupon10 = DiscountCoupon::create([
                "project_id" => $projectCreated->id,
                "name" => "Desconto 10%",
                "type" => 0,
                "value" => 10,
                "code" => "NEXX10",
                "status" => 1,
                "rule_value" => 0,
                "recovery_flag" => true,
            ]);

            $discountCoupon20 = DiscountCoupon::create([
                "project_id" => $projectCreated->id,
                "name" => "Desconto 20%",
                "type" => 0,
                "value" => 20,
                "code" => "NEXX20",
                "status" => 1,
                "rule_value" => 0,
                "recovery_flag" => true,
            ]);

            if (empty($discountCoupon10) || empty($discountCoupon20)) {
                $shippingCreated->delete();
                $projectCreated->delete();

                return response()->json(
                    [
                        "message" => "Problema ao criar integração, tente novamente mais tarde",
                    ],
                    400,
                );
            }

            $shopifyIntegrationCreated = ShopifyIntegration::create([
                "token" => $dataRequest["token"],
                "shared_secret" => "",
                "url_store" => $urlStore . ".myshopify.com",
                "user_id" => auth()->user()->account_owner_id,
                "project_id" => $projectCreated->id,
                "status" => ShopifyIntegration::STATUS_PENDING,
            ]);

            if (empty($shopifyIntegrationCreated)) {
                $shippingCreated->delete();
                $discountCoupon10->delete();
                $discountCoupon20->delete();
                $projectCreated->delete();

                return response()->json(
                    [
                        "message" => "Problema ao criar integração, tente novamente mais tarde",
                    ],
                    400,
                );
            }

            $userProjectCreated = UserProject::create([
                "user_id" => auth()->user()->account_owner_id,
                "project_id" => $projectCreated->id,
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
                $shopifyIntegrationCreated->delete();
                $shippingCreated->delete();
                $discountCoupon10->delete();
                $discountCoupon20->delete();
                $projectCreated->delete();

                return response()->json(
                    [
                        "message" => "Problema ao criar integração, tente novamente mais tarde",
                    ],
                    400,
                );
            }

            (new ProjectNotificationService())->createProjectNotificationDefault($projectCreated->id);
            (new ProjectService())->createUpsellConfig($projectCreated->id);

            dispatch(new ImportShopifyProductsStore($shopifyIntegrationCreated, auth()->user()->account_owner_id));

            TaskService::setCompletedTask(auth()->user(), Task::find(Task::TASK_CREATE_FIRST_STORE));

            return response()->json(
                [
                    "message" => "Integração em andamento. Assim que tudo estiver pronto você será avisado(a)!",
                ],
                200,
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Problema ao criar integração, tente novamente mais tarde"], 400);
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            $dataRequest = $request->all();

            if (!isset($dataRequest["id"])) {
                return response()->json(["message" => "É necessário enviar o id da integração"], 400);
            }

            $projectId = current(Hashids::decode($dataRequest["id"]));
            if (!$projectId) {
                return response()->json(["message" => "ID de integração inválido"], 400);
            }
            
            $shopifyIntegration = ShopifyIntegration::where('project_id', $projectId)->first();

            if (empty($shopifyIntegration)) {
                return response()->json(["message" => "Integração não encontrada!"], 400);
            }

            // Delete the shop record
            $shopifyIntegration->delete();

            return response()->json(['message' => 'Shopify excluída com sucesso!'], 200);

            return response()->json(
                [
                    "message" => "Integração excluída!",
                ],
                200,
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Problema ao criar integração, tente novamente mais tarde"], 400);
        }
    }
    
    public function undoIntegration(Request $request)
    {
        try {
            $requestData = $request->all();

            $projectId = current(Hashids::decode($requestData["project_id"]));

            $projectModel = new Project();

            if (FoxUtils::isEmpty($projectId)) {
                return response()->json(["message" => "Projeto não encontrado"], Response::HTTP_BAD_REQUEST);
            }

            $project = $projectModel->with(["shopifyIntegrations"])->find($projectId);

            if (empty($project->shopify_id)) {
                return response()->json(
                    [
                        "message" => "Este projeto não tem integração com o shopify",
                    ],
                    400,
                );
            }

            activity()
                ->on($projectModel)
                ->tap(function (Activity $activity) use ($projectId) {
                    $activity->log_name = "updated";
                    $activity->subject_id = current(Hashids::decode($projectId));
                })
                ->log("Integração com o shopify desfeita para o projeto " . $project->name);

            try {
                foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                    $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                    $shopify->templateService->removeIntegrationInAllThemes();

                    //remove todos os webhooks
                    $shopify->deleteShopWebhook();

                    $shopifyIntegration->update([
                        "status" => $shopifyIntegration->present()->getStatus("disabled"),
                    ]);
                }

                return response()->json(["message" => "Integração com o shopify desfeita"], Response::HTTP_OK);
            } catch (Exception $e) {
                return response()->json(
                    [
                        "message" => "Problema ao desfazer integração, tente novamente mais tarde",
                    ],
                    400,
                );
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Problema ao desfazer integração, tente novamente mais tarde",
                ],
                400,
            );
        }
    }

    public function reIntegration(Request $request): JsonResponse
    {
        try {
            $requestData = $request->all();

            $projectId = current(Hashids::decode($requestData["project_id"]));
            $projectModel = new Project();
            $shopifyIntegrationModel = new ShopifyIntegration();
            $domainModel = new Domain();

            if ($projectId) {
                //id decriptado
                $project = $projectModel
                    ->with([
                        "domains",
                        "shopifyIntegrations",
                        "plans",
                        "plans.productsPlans",
                        "plans.productsPlans.product",
                        "pixels",
                        "discountCoupons",
                        "shippings",
                    ])
                    ->find($projectId);

                activity()
                    ->on($shopifyIntegrationModel)
                    ->tap(function (Activity $activity) {
                        $activity->log_name = "updated";
                    })
                    ->log("Reintegração do shopify para o projeto " . $project->name);

                //puxa todos os produtos
                foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                    if (0 === preg_match('/^([a-zA-Z0-9_]{10,100})$/', $shopifyIntegration->token)) {
                        return response()->json(
                            ["message" => "O token deve ter entre 10 e 100 letras e números!"],
                            400,
                        );
                    }

                    $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
                    $shopify->importShopifyStore($projectId, auth()->user()->account_owner_id);
                }

                //procura por um dominio aprovado
                $domain = $project->domains->where("status", $domainModel->present()->getStatus("approved"))->first();

                if (!empty($domain)) {
                    //primeiro dominio valido
                    if (!empty($project->shopify_id)) {
                        //se for shopify, voltar as integraçoes ao html padrao
                        try {
                            foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                                $shopify = new ShopifyService(
                                    $shopifyIntegration->url_store,
                                    $shopifyIntegration->token,
                                );

                                $basicTheme = $shopifyIntegrationModel->present()->getThemeType("basic_theme");

                                $shopify->templateService->makeTemplateIntegration(
                                    $shopifyIntegration,
                                    $domain,
                                    $basicTheme,
                                );
                            }

                            return response()->json(
                                ["message" => "Integração com o shopify refeita"],
                                Response::HTTP_OK,
                            );
                        } catch (Exception $e) {
                            //throwl
                            return response()->json(
                                ["message" => "Problema ao refazer integração, tente novamente mais tarde"],
                                Response::HTTP_BAD_REQUEST,
                            );
                        }
                    } else {
                        return response()->json(
                            ["message" => "Este projeto não tem integração com o shopify"],
                            Response::HTTP_BAD_REQUEST,
                        );
                    }
                } else {
                    //nenhum dominio ativado
                    return response()->json(
                        [
                            "message" =>
                                "Produtos do shopify importados, adicione um domínio para finalizar a sua integração",
                        ],
                        Response::HTTP_OK,
                    );
                }
            } else {
                //problema no id
                return response()->json(["message" => "Projeto não encontrado"], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(
                ["message" => "Problema ao refazer integração, tente novamente mais tarde"],
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function synchronizeProducts(Request $request): JsonResponse
    {
        try {
            $requestData = $request->all();

            $projectId = hashids_decode($requestData["project_id"]);
            $project = Project::find($projectId);

            activity()
                ->on(new ShopifyIntegration())
                ->tap(function (Activity $activity) {
                    $activity->log_name = "updated";
                })
                ->log("Sicronizou produtos do shopify para o projeto " . $project->name);

            $shopifyIntegration = ShopifyIntegration::where("project_id", $projectId)->first();
            if (empty($shopifyIntegration)) {
                return response()->json(
                    ["message" => "Problema ao sincronizar produtos, tente novamente mais tarde"],
                    Response::HTTP_BAD_REQUEST,
                );
            }
            if (0 === preg_match('/^([a-zA-Z0-9_]{10,100})$/', $shopifyIntegration->token)) {
                return response()->json(["message" => "O token deve ter entre 10 e 100 letras e números!"], 400);
            }

            dispatch(new ImportShopifyProductsStore($shopifyIntegration, auth()->user()->account_owner_id));

            return response()->json(
                ["message" => "Os Produtos do shopify estão sendo sincronizados."],
                Response::HTTP_OK,
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(
                ["message" => "Problema ao sincronizar produtos do shopify, tente novamente mais tarde"],
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function synchronizeTrackings(Request $request): JsonResponse
    {
        try {
            $requestData = $request->all();
            $projectModel = new Project();

            $project = $projectModel->find(current(Hashids::decode($requestData["project_id"])));

            ImportShopifyTrackingCodesJob::dispatch($project);

            return response()->json(
                [
                    "message" => "Os códigos de rastreio estão sendo importados...",
                ],
                Response::HTTP_OK,
            );
        } catch (Exception $e) {
            if (method_exists($e, "getCode") && in_array($e->getCode(), [401, 402, 403, 404, 406, 422, 423, 429])) {
                return response()->json(
                    ["message" => "Problema ao sincronizar códigos de rastreio do shopify, tente novamente mais tarde"],
                    Response::HTTP_BAD_REQUEST,
                );
            }
            report($e);

            return response()->json(
                ["message" => "Problema ao sincronizar códigos de rastreio do shopify, tente novamente mais tarde"],
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function synchronizeTemplates(Request $request): JsonResponse
    {
        try {
            if (!foxutils()->isProduction()) {
                return response()->json(["message" => "Alteração permitida somente em produção!"], 400);
            }

            $requestData = $request->all();

            $projectModel = new Project();
            $shopifyIntegrationModel = new ShopifyIntegration();
            $domainModel = new Domain();

            $projectId = current(Hashids::decode($requestData["project_id"]));

            if (empty($projectId)) {
                return response()->json(["message" => "Projeto não encontrado"], Response::HTTP_BAD_REQUEST);
            }

            $project = $projectModel
                ->with([
                    "domains",
                    "shopifyIntegrations",
                    "plans",
                    "plans.productsPlans",
                    "plans.productsPlans.product",
                    "pixels",
                    "discountCoupons",
                    "shippings",
                ])
                ->find($projectId);

            //            $domain = new \stdClass();
            //            $domain->name = "azcend.com.br";
            if (\foxutils()->isProduction()) {
                $domain = $project->domains->where("status", $domainModel->present()->getStatus("approved"))->first();
            }

            if (empty($domain)) {
                return response()->json(
                    ["message" => "Você não tem nenhum domínio configurado"],
                    Response::HTTP_BAD_REQUEST,
                );
            }

            if (empty($project->shopify_id)) {
                return response()->json(
                    ["message" => "Este projeto não tem integração com o shopify"],
                    Response::HTTP_BAD_REQUEST,
                );
            }

            activity()
                ->on($shopifyIntegrationModel)
                ->tap(function (Activity $activity) {
                    $activity->log_name = "updated";
                })
                ->log("Sicronizou template do shopify para o projeto " . $project->name);

            try {
                foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                    if (0 === preg_match('/^([a-zA-Z0-9_]{10,100})$/', $shopifyIntegration->token)) {
                        return response()->json(
                            ["message" => "O token deve ter entre 10 e 100 letras e números!"],
                            400,
                        );
                    }
                    $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                    $basicTheme = $shopifyIntegrationModel->present()->getThemeType("basic_theme");

                    $shopify->templateService->makeTemplateIntegration($shopifyIntegration, $domain, $basicTheme);
                }

                return response()->json(
                    [
                        "message" => "Sincronização do template com o shopify concluida com sucesso!",
                    ],
                    Response::HTTP_OK,
                );
            } catch (Exception $e) {
                $message = ShopifyErrors::FormatErrors($e->getMessage());

                if (empty($message)) {
                    report($e);
                    $message = "Problema ao refazer integração, tente novamente mais tarde";
                }

                return response()->json(["message" => $message], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(
                ["message" => "Problema ao sincronizar template do shopify, tente novamente mais tarde"],
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function getCompanies()
    {
        try {
            $companyModel = new Company();
            $companies = $companyModel->where("user_id", auth()->user()->account_owner_id)->get();

            return CompaniesSelectResource::collection($companies);
        } catch (Exception $e) {
            Log::warning("Erro ao tentar abrir modal de integração shopify");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro, tente novamente mais tarde",
                ],
                400,
            );
        }
    }

    public function updateToken(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            if (empty($data["token"]) || 0 === preg_match('/^([a-zA-Z0-9_]{10,100})$/', $data["token"])) {
                return response()->json(
                    ["message" => "Token inválido, o token de acesso deve ter entre 10 e 100 letras e números"],
                    400,
                );
            }

            $project = Project::find(hashids_decode($data["project_id"]));

            if (empty($project)) {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro ao atualizar token, tente novamente mais tarde",
                    ],
                    400,
                );
            }

            activity()
                ->on(new ShopifyIntegration())
                ->tap(function (Activity $activity) {
                    $activity->log_name = "updated";
                })
                ->log("Atualizou token de integração do shopify para o projeto " . $project->name);

            $integration = ShopifyIntegration::where("project_id", $project->id)->first();

            $shopify = new ShopifyService($integration->url_store, $data["token"]);

            $permissions = $shopify->verifyPermissions();

            if ($permissions["status"] == "error") {
                return response()->json(
                    [
                        "message" => $permissions["message"],
                    ],
                    400,
                );
            }

            $integrationUpdated = $integration->update(["token" => $data["token"]]);

            if ($integrationUpdated) {
                return response()->json(["message" => "Token atualizado com sucesso"], 200);
            }

            return response()->json(
                [
                    "message" => "Ocorreu um erro ao atualizar token, tente novamente mais tarde",
                ],
                400,
            );
        } catch (Exception $e) {
            $message = ShopifyErrors::FormatErrors($e->getMessage());

            if (empty($message)) {
                report($e);
                $message = "Ocorreu um erro ao atualizar token, tente novamente mais tarde";
            }

            return response()->json(
                [
                    "message" => $message,
                ],
                400,
            );
        }
    }

    public function verifyPermission(Request $request): JsonResponse
    {
        $data = $request->all();

        if (empty($data["project_id"])) {
            return response()->json(
                [
                    "message" => "Ocorreu um erro ao verificar permissões, tente novamente mais tarde",
                ],
                400,
            );
        }

        $project = Project::find(hashids_decode($data["project_id"]));

        if (empty($project)) {
            return response()->json(
                [
                    "message" => "Ocorreu um erro ao verificar permissões, tente novamente mais tarde",
                ],
                400,
            );
        }

        activity()
            ->on(new ShopifyIntegration())
            ->tap(function (Activity $activity) {
                $activity->log_name = "visualization";
            })
            ->log("Verificação de permissões do token de integração do shopify para o projeto: " . $project->name);

        $integration = ShopifyIntegration::where("project_id", $project->id)->first();

        if (0 === preg_match('/^([a-zA-Z0-9_]{10,100})$/', $integration->token)) {
            return response()->json(["message" => "O token deve ter entre 10 e 100 letras e números!"], 400);
        }

        $shopify = new ShopifyService($integration->url_store, $integration->token);
        $permissions = $shopify->verifyPermissions();

        if ($permissions["status"] == "error") {
            return response()->json(
                [
                    "message" => $permissions["message"],
                ],
                400,
            );
        }

        return response()->json(
            [
                "message" => "Todas as permissões estão funcionando corretamente",
            ],
            200,
        );
    }

    public function setSkipToCart(Request $request): JsonResponse
    {
        try {
            if (!foxutils()->isProduction()) {
                return response()->json(["message" => "Alteração permitida somente em produção!"], 400);
            }

            $data = $request->all();

            if (empty($data["project_id"]) || !isset($data["skip_to_cart"])) {
                return response()->json(["message" => "Ocorreu um erro ao atualizar o skip to cart do projeto"], 400);
            }

            $project = Project::with(["domains", "shopifyIntegrations"])->find(hashids_decode($data["project_id"]));

            $integration = $project->shopifyIntegrations->first();

            if (0 === preg_match('/^([a-zA-Z0-9_]{10,100})$/', $integration->token)) {
                return response()->json(["message" => "O token deve ter entre 10 e 100 letras e números!"], 400);
            }

            $shopify = new ShopifyService($integration->url_store, $integration->token);

            $shopify->templateService->setSkipToCart((bool) $data["skip_to_cart"]);

            DB::beginTransaction();

            $integration->update([
                "skip_to_cart" => (bool) $data["skip_to_cart"],
            ]);

            activity()
                ->on(new Project())
                ->tap(function (Activity $activity) use ($project) {
                    $activity->log_name = "updated";
                    $activity->subject_id = $project->id;
                })
                ->log("Skip to cart atualizado no projeto " . $project->name);

            DB::commit();
            return response()->json(["message" => "Skip to cart atualizado no projeto"]);
        } catch (Exception $e) {
            DB::rollBack();
            if (!method_exists($e, "getCode") || !in_array($e->getCode(), [401, 402, 403, 404])) {
                report($e);
            }

            return response()->json(["message" => "Ocorreu um erro ao atualizar o skip to cart do projeto"], 400);
        }
    }
}
