<?php

declare(strict_types=1);

namespace Modules\Projects\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;
use Modules\Core\Transformers\CompaniesSelectResource;
use Modules\Core\Transformers\CompanyResource;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\CheckoutConfig;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\PixelConfig;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\TaskService;
use Modules\Projects\Exceptions\CannotDeleteProjectException;
use Modules\Projects\Http\Requests\ProjectStoreRequest;
use Modules\Projects\Http\Requests\ProjectsSettingsUpdateRequest;
use Modules\Projects\Transformers\ProjectsResource;
use Modules\Projects\Transformers\UserProjectResource;
use Modules\Shopify\Transformers\ShopifyIntegrationsResource;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ProjectsApiController
 * @package Modules\Projects\Http\Controllers
 */
class ProjectsApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $hasCompany = Company::where("user_id", $user->getAccountOwnerId())->exists();

            if ($hasCompany) {
                $projectModel = new Project();
                $projectService = new ProjectService();
                $pagination = (bool) $request->input("select", false);
                $affiliation = true;

                if (!empty($request->input("affiliate")) && $request->input("affiliate") == "false") {
                    $affiliation = false;
                }

                if (!$pagination) {
                    activity()
                        ->on($projectModel)
                        ->tap(function (Activity $activity) {
                            $activity->log_name = "visualization";
                        })
                        ->log("Visualizou tela todos os projetos");
                }

                if ($request->input("status")) {
                    if ($request->input("status") == "all") {
                        $projectStatus = [
                            $projectModel->present()->getStatus("active"),
                            $projectModel->present()->getStatus("disabled"),
                        ];
                    } else {
                        $projectStatus = [$projectModel->present()->getStatus($request->input("status"))];
                    }
                } else {
                    $projectStatus = [$projectModel->present()->getStatus("active")];
                    if ($user->deleted_project_filter) {
                        $projectStatus = [
                            $projectModel->present()->getStatus("active"),
                            $projectModel->present()->getStatus("disabled"),
                        ];
                    }
                }

                $companyId = $user->company_default;
                if (!empty($request->input("company"))) {
                    $companyId = hashids_decode($request->input("company"));
                }

                if ($request->tokens) {
                    return $projectService->getUserProjectsAndTokens(
                        $pagination,
                        $projectStatus,
                        $affiliation,
                        $companyId
                    );
                }

                return $projectService->getUserProjects($pagination, $projectStatus, $affiliation, $companyId);
            }

            return response()->json([
                "data" => [],
                "no_company" => true,
                "message" => "Nenhuma empresa cadastrada!",
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Erro ao tentar acessar projetos"], 400);
        }
    }

    public function create(): JsonResponse
    {
        try {
            activity()
                ->tap(function (Activity $activity) {
                    $activity->log_name = "visualization";
                })
                ->log("Visualizou tela criar projeto");

            $user = auth()->user();
            $companies = Company::where("user_id", $user->getAccountOwnerId())
                ->where("active_flag", true)
                ->get();

            return response()->json(CompaniesSelectResource::collection($companies));
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Erro ao carregar empresas"], 400);
        }
    }

    public function store(ProjectStoreRequest $request): JsonResponse
    {
        try {
            $requestValidated = $request->validated();

            $projectModel = new Project();
            $userProjectModel = new UserProject();
            $shippingModel = new Shipping();
            $amazonFileService = app(AmazonFileService::class);

            if (empty($requestValidated)) {
                return response()->json(["message" => "Erro ao tentar salvar projeto"], 400);
            }

            $project = $projectModel->create([
                "name" => $requestValidated["name"],
                "description" => $requestValidated["description"],
                "visibility" => "private",
                "automatic_affiliation" => 0,
                "status" => $projectModel->present()->getStatus("active"),
                "notazz_configs" => json_encode([
                    "cost_currency_type" => 1,
                    "update_cost_shopify" => 1,
                ]),
            ]);

            if (empty($project)) {
                return response()->json(["message" => "Erro ao tentar salvar projeto"], 400);
            }

            Domain::create([
                "project_id" => $project->id,
                "cloudflare_domain_id" => null,
                "name" => "pag.net.br",
                "status" => 3,
                "sendgrid_id" => null,
                "created_at" => now(),
                "updated_at" => now(),
                "deleted_at" => null,
            ]);

            DiscountCoupon::create([
                "project_id" => $project->id,
                "name" => "Desconto 10%",
                "type" => 0,
                "value" => 10,
                "code" => "NEXX10",
                "status" => 1,
                "rule_value" => 0,
                "recovery_flag" => true,
            ]);

            DiscountCoupon::create([
                "project_id" => $project->id,
                "name" => "Desconto 20%",
                "type" => 0,
                "value" => 20,
                "code" => "NEXX20",
                "status" => 1,
                "rule_value" => 0,
                "recovery_flag" => true,
            ]);

            $company = Company::find(hashids_decode($requestValidated["company"]));
            $bankAccount = $company->getDefaultBankAccount();

            $checkoutConfig = CheckoutConfig::create([
                "company_id" => $company->id,
                "project_id" => $project->id,
                "pix_enabled" => !!(!empty($bankAccount) && $bankAccount->transfer_type == "PIX"),
            ]);

            if (empty($checkoutConfig)) {
                $project->delete();
                return response()->json(["message" => "Erro ao tentar salvar projeto"], 400);
            }

            PixelConfig::create(["project_id" => $project->id]);

            $shipping = $shippingModel->create([
                "project_id" => $project->id,
                "name" => "Frete gratis",
                "information" => "de 15 até 30 dias",
                "value" => "0,00",
                "type" => "static",
                "type_enum" => $shippingModel->present()->getTypeEnum("static"),
                "status" => "1",
                "pre_selected" => "1",
                "apply_on_plans" => '["all"]',
                "not_apply_on_plans" => "[]",
            ]);

            if (empty($shipping)) {
                $project->delete();

                return response()->json(["message" => "Erro ao tentar salvar projeto"], 400);
            }

            $photo = $request->file("photo");
            if ($photo != null) {
                try {
                    $img = Image::make($photo->getPathname());
                    $img->save($photo->getPathname());

                    $amazonPath = $amazonFileService->uploadFile(
                        "uploads/user/".
                        Hashids::encode(auth()->user()->account_owner_id).
                        "/public/projects/".
                        Hashids::encode($project->id).
                        "/main",
                        $photo
                    );
                    $project->update(["photo" => $amazonPath]);
                } catch (Exception $e) {
                    report($e);
                }
            }

            $userProject = $userProjectModel->create([
                "user_id" => auth()->user()->account_owner_id,
                "project_id" => $project->id,
                "company_id" => $company->id,
                "type" => "producer",
                "type_enum" => $userProjectModel->present()->getTypeEnum("producer"),
                "access_permission" => 1,
                "edit_permission" => 1,
                "status" => "active",
                "status_flag" => $userProjectModel->present()->getStatusFlag("active"),
            ]);

            if (empty($userProject)) {
                if (!empty($amazonPath)) {
                    $amazonPath->deleteFile($project->photo);
                }
                $shipping->delete();
                $project->delete();

                return response()->json(["message" => "Erro ao tentar salvar projeto"], 400);
            }

            $projectNotificationService = new ProjectNotificationService();
            $projectService = new ProjectService();

            $projectNotificationService->createProjectNotificationDefault($project->id);
            $projectService->createUpsellConfig($project->id);

            TaskService::setCompletedTask(auth()->user(), Task::find(Task::TASK_CREATE_FIRST_STORE));

            return response()->json(["message" => "Projeto salvo com sucesso"]);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Erro ao tentar salvar projeto"], 400);
        }
    }

    public function edit($id): JsonResponse
    {
        try {
            $user = User::with("companies")->find(auth()->user()->account_owner_id);

            $project = Project::with([
                "usersProjects",
                "usersProjects.company" => function ($query) use ($user) {
                    $query->where("user_id", $user->account_owner_id);
                },
            ])->find(hashids_decode($id));

            activity()
                ->on(new Project())
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = current(Hashids::decode($id));
                })
                ->log("Visualizou tela editar configurações do projeto ".$project->name);

            $userProject = UserProject::where("user_id", $user->account_owner_id)
                ->where("project_id", hashids_decode($id))
                ->first();
            $userProject = new UserProjectResource($userProject);

            $shopifyIntegrations = ShopifyIntegration::where("user_id", $user->account_owner_id)
                ->where("project_id", hashids_decode($id))
                ->get();
            $shopifyIntegrations = ShopifyIntegrationsResource::collection($shopifyIntegrations);

            $companies = CompaniesSelectResource::collection($user->companies);

            if (Gate::allows("edit", [$project])) {
                $project = new ProjectsResource($project);

                return response()->json(compact("companies", "project", "userProject", "shopifyIntegrations"));
            }
            return response()->json(["message" => "Erro ao carregar configurações do projeto"], 400);
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Erro ao carregar configurações do projeto",
                ],
                400
            );
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $projectModel = new Project();
            $projectId = current(Hashids::decode($id));

            activity()
                ->on($projectModel)
                ->tap(function (Activity $activity) use ($projectId) {
                    $activity->log_name = "deleted";
                    $activity->subject_id = $projectId;
                })
                ->log("deleted");

            $project = $projectModel->where("id", $projectId)->first();

            if (Gate::allows("destroy", [$project])) {
                $projectService = new ProjectService();

                if ($projectId) {
                    if ($projectService->delete($projectId)) {
                        return response()->json("success", 200);
                    }
                    return response()->json("error", 400);
                }
                return response()->json("Projeto não encontrado", 400);
            }
            return response()->json("Sem permissão para remover projeto", 403);
        } catch (CannotDeleteProjectException) {
            return response()->json([
                'message' => 'O projeto não pode ser removido.'
            ], Response::HTTP_CONFLICT);
        } catch (Exception $e) {
            report($e);

            return response()->json("Erro ao remover o projeto, tente novamente mais tarde", 400);
        }
    }

    public function updateSettings(ProjectsSettingsUpdateRequest $request, $id)
    {
        try {
            $requestValidated = $request->validated();
            $projectModel = new Project();
            $amazonFileService = app(AmazonFileService::class);

            if (!$requestValidated) {
                return response()->json(["message" => "Erro ao atualizar projeto"], 400);
            }

            $projectId = current(Hashids::decode($id));
            $project = $projectModel->find($projectId);

            if (!Gate::allows("update", [$project])) {
                return response()->json(["message" => "Sem permissão para atualizar o projeto"], 403);
            }
            $requestValidated["status"] = 1;

            $projectPhoto = $request->file("project_photo");
            $removeProjectPhoto = $request->get("remove_project_photo");

            if ($projectPhoto == null && $removeProjectPhoto == "true") {
                try {
                    $amazonFileService->deleteFile($project->photo);
                    $project->update(["photo" => null]);
                } catch (Exception $error) {
                    report($error);
                }
            }

            if ($projectPhoto != null && !$removeProjectPhoto) {
                try {
                    $amazonFileService->deleteFile($project->photo);
                    $img = Image::make($projectPhoto->getPathname());
                    $img->save($projectPhoto->getPathname());

                    $amazonPath = $amazonFileService->uploadFile(
                        "uploads/user/".
                        Hashids::encode(auth()->user()->account_owner_id).
                        "/public/project/".
                        Hashids::encode($project->id).
                        "/main",
                        $projectPhoto
                    );

                    $project->update(["photo" => $amazonPath]);
                } catch (Exception $error) {
                    report($error);
                    return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
                }
            }

            $projectUpdate = $project->update($requestValidated);
            if (!$projectUpdate) {
                return response()->json(["message" => "Erro ao atualizar projeto"], 400);
            }

            return response()->json(["message" => "Projeto atualizado!"], 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao atualizar projeto"], 400);
        }
    }

    public function show($id)
    {
        try {
            $userId = auth()->user()->account_owner_id;

            if (empty($id)) {
                return response()->json(
                    [
                        "message" => "Erro ao exibir detalhes do projeto",
                        "account_is_approved" => (bool)auth()->user()->account_is_approved,
                    ],
                    400
                );
            }

            $id = hashids_decode($id);
            $project = Project::where("id", $id)
                ->where("status", Project::STATUS_ACTIVE)
                ->with([
                    "affiliates" => function ($query) use ($userId) {
                        $query->where("user_id", $userId);
                    },
                    "usersProjects.company",
                    'apiToken',
                ])
                ->first();

            if (empty($project)) {
                return response()->json(
                    [
                        "message" => "Projeto não encontrado!",
                        "account_is_approved" => (bool)auth()->user()->account_is_approved,
                    ],
                    400
                );
            }

            $resume = $this->getProjectResume($project->id, $userId);

            $project->chargeback_count = $resume["chargeback_count"];
            $project->without_tracking = $resume["without_tracking"];
            $project->approved_sales = $resume["approved_sales"];
            $project->approved_sales_value = $resume["approved_sales_value"];
            $project->open_tickets = $resume["open_tickets"];
            $project->producer = $resume["producer"];

            if (Gate::allows("show", [$project])) {
                activity()
                    ->on(new Project())
                    ->tap(function (Activity $activity) use ($id) {
                        $activity->log_name = "visualization";
                        $activity->subject_id = $id;
                    })
                    ->log("Visualizou o projeto ".$project->name);

                return new ProjectsResource($project);
            }

            return response()->json(["message" => "Erro ao exibir detalhes do projeto"], 400);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao exibir detalhes do projeto"], 400);
        }
    }

    public function getProjectResume($projectId, $userOwnerId)
    {
        $resume = [
            "chargeback_count" => 0,
            "without_tracking" => 0,
            "approved_sales" => 0,
            "approved_sales_value" => 0,
            "open_tickets" => 0,
            "producer" => "",
        ];

        $resume["chargeback_count"] = Sale::where("project_id", $projectId)
            ->where("status", Sale::STATUS_CHARGEBACK)
            ->count();

        $resume["without_tracking"] = Sale::where("project_id", $projectId)
            ->where("has_valid_tracking", false)
            ->whereNotNull("delivery_id")
            ->where("status", Sale::STATUS_APPROVED)
            ->count();

        $resume["approved_sales"] = Sale::where("project_id", $projectId)
            ->where("status", Sale::STATUS_APPROVED)
            ->count();

        $resume["approved_sales_value"] = Transaction::where("user_id", $userOwnerId)
            ->whereHas("sale", function ($query) use ($projectId) {
                $query->where("status", Sale::STATUS_APPROVED);
                $query->where("project_id", $projectId);
            })
            ->sum("value");

        $resume["open_tickets"] = Sale::where("project_id", $projectId)
            ->whereHas("tickets", function ($query) {
                $query->where("ticket_status_enum", Ticket::STATUS_OPEN);
            })
            ->count();

        $producer = User::select("name")
            ->whereHas("usersProjects", function ($query) use ($projectId) {
                $query->where("project_id", $projectId)->where("type_enum", UserProject::TYPE_PRODUCER_ENUM);
            })
            ->first();

        $resume["producer"] = $producer->name ?? "";

        return $resume;
    }

    public function getCompanieByProject($id)
    {
        try {
            $projectID = hashids_decode($id);

            $projectModel = new Project();
            $project = $projectModel->with("usersProjects.company")->find($projectID);

            return new CompanyResource($project->usersProjects->first()->company);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro ao buscar os dados do projeto"], 400);
        }
    }

    public function getProjects()
    {
        try {
            $projectService = new ProjectService();
            $projectModel = new Project();

            $projectStatus = [$projectModel->present()->getStatus("active")];

            return $projectService->getUserProjects(true, $projectStatus, true);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro ao buscar dados das empresas"], 400);
        }
    }

    public function updateOrder(Request $request): JsonResponse
    {
        try {
            $orders = $request->input("order");
            $page = $request->page ?? 1;
            $paginate = $request->paginate ?? 100;
            $initOrder = $page * $paginate - $paginate + 1;

            $projectIds = [];

            foreach ($orders as $order) {
                $projectIds[] = current(Hashids::decode($order));
            }

            $projects = UserProject::whereIn("project_id", collect($projectIds))
                ->where("user_id", auth()->user()->account_owner_id)
                ->get();

            $affiliates = Affiliate::whereIn("project_id", collect($projectIds))
                ->where("user_id", auth()->user()->account_owner_id)
                ->get();

            foreach ($projectIds as $value) {
                $project = $projects->firstWhere("project_id", $value);
                if (isset($project->id)) {
                    $project->update(["order_priority" => $initOrder]);
                } else {
                    $affiliate = $affiliates->firstWhere("project_id", $value);
                    if (isset($affiliate->id)) {
                        $affiliate->update(["order_priority" => $initOrder]);
                    }
                }
                $initOrder++;
            }

            return response()->json(["message" => "Ordenação atualizada com sucesso"], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Erro ao atualizar ordenação"], 400);
        }
    }

    public function updateConfig(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $user = auth()->user();

            $updated = $user->update([
                "deleted_project_filter" => $data["deleted_project_filter"],
            ]);

            if ($updated) {
                return response()->json(["message" => "Configuração atualizada com sucesso"], 200);
            }
            return response()->json(["message" => "Erro ao atualizar configuração"], 400);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao atualizar configuração"], 400);
        }
    }
}
