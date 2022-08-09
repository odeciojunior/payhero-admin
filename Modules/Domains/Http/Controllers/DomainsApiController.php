<?php

namespace Modules\Domains\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\DomainRecord;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Task;
use Modules\Core\Services\CloudflareErrorsService;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\DomainService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\TaskService;
use Modules\Domains\Http\Requests\DomainDestroyRequest;
use Modules\Domains\Http\Requests\DomainStoreRequest;
use Modules\Domains\Transformers\DomainResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class DomainsApiController
 * @package Modules\Dominios\Http\Controllers
 */
class DomainsApiController extends Controller
{
    public function index($projectId)
    {
        try {
            $projectId = hashids_decode($projectId);
            $project = Project::find($projectId);

            if (empty($project)) {
                return response()->json(["message" => "Erro ao listar dados de domínios"], 400);
            }

            activity()
                ->on(new Domain())
                ->tap(function (Activity $activity) use ($projectId) {
                    $activity->log_name = "visualization";
                })
                ->log("Visualizou tela todos os domínios para o projeto: " . $project->name);

            if (!Gate::allows("index", [$project])) {
                return response()->json(["message" => "Sem permissão para visualizar os domínios"], 400);
            }

            $domains = Domain::with("project")->where("project_id", $projectId);

            return DomainResource::collection($domains->orderBy("id", "DESC")->paginate(5));
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Erro ao listar dados de domínios",
                ],
                400
            );
        }
    }

    public function store(DomainStoreRequest $request): JsonResponse
    {
        try {
            $cloudFlareService = new CloudFlareService();

            DB::beginTransaction();
            $requestData = $request->validated();

            $projectId = $requestData["project_id"] ?? null;
            $projectId = hashids_decode($projectId);

            if (empty($projectId)) {
                DB::rollBack();

                return response()->json(["message" => "Projeto não encontrado."], 400);
            }

            $project = Project::with(["domains"])->find($projectId);

            if (!Gate::allows("edit", [$project])) {
                DB::rollBack();

                return response()->json(["message" => "Sem permissão para criar um domínio neste projeto"], 403);
            }

            if (!empty($project->shopify_id)) {
                //projeto shopify
                $domainIp = $cloudFlareService::shopifyIp;
            } else {
                //projeto web
                $domainIp = null;
            }

            //tratamento parcial do dominio
            $requestData["name"] = str_replace("http://", "", $requestData["name"]);
            $requestData["name"] = str_replace("https://", "", $requestData["name"]);
            $requestData["name"] = str_replace("www.", "", $requestData["name"]);
            $requestData["name"] = "http://" . $requestData["name"];
            $requestData["name"] = parse_url($requestData["name"], PHP_URL_HOST);

            if (Domain::where("name", $requestData["name"])->count() > 0) {
                DB::rollBack();

                return response()->json(["message" => "Domínio já está sendo utilizado"], 400);
            }

            if ($project->domains->where("name", $requestData["name"])->count() != 0) {
                DB::rollBack();

                return response()->json(["message" => "Domínios já cadastrado."], 400);
            }

            if (!empty($cloudFlareService->getZones($requestData["name"]))) {
                DB::rollBack();

                return response()->json(["message" => "Domínio já está sendo utilizado"], 400);
            }

            $domainCreated = Domain::create([
                "project_id" => $projectId,
                "name" => $requestData["name"],
                "domain_ip" => $domainIp,
                "status" => (new Domain())->present()->getStatus("pending"),
            ]);

            if (empty($domainCreated)) {
                DB::rollBack();

                return response()->json(["message" => "Erro ao cadastrar domínio."], 400);
            }

            if ($project->shopify_id == null) {
                $newDomain = $cloudFlareService->integrationWebsite(
                    $domainCreated->id,
                    $requestData["name"],
                    $domainIp
                );
            } else {
                $newDomain = $cloudFlareService->integrationShopify($domainCreated->id, $requestData["name"]);
                $requestData["domain_ip"] = "Domínio Shopify";
            }

            $setCloudFlareConfig = $cloudFlareService->setCloudFlareConfig($requestData["name"]);

            if (!$setCloudFlareConfig) {
                DB::rollBack();

                return response()->json(["message" => "Domínio inválido!"], 400);
            }

            if (empty($newDomain)) {
                DB::rollBack();

                return response()->json(["message" => "Erro ao criar domínio."], 400);
            }

            try {
                TaskService::setCompletedTask($project->users->first(), Task::find(Task::TASK_DOMAIN_APPROVED));
            } catch (Exception $e) {
                report($e);
            }

            DB::commit();

            $newNameServers = [];
            foreach ($cloudFlareService->getZones() as $zone) {
                if ($zone->name == $domainCreated->name) {
                    foreach ($zone->name_servers as $new_name_server) {
                        $newNameServers[] = $new_name_server;
                    }
                }
            }

            return response()->json(
                [
                    "message" => "Domínio cadastrado com sucesso",
                    "data" => [
                        "id_code" => Hashids::encode($domainCreated->id),
                        "zones" => $newNameServers,
                    ],
                ],
                200
            );
        } catch (Exception $e) {
            if (!empty($domainCreated)) {
                (new CloudFlareService())->removeDomain($domainCreated);
            }

            DB::rollBack();

            report($e);
            $message = CloudflareErrorsService::formatErrorException($e);

            return response()->json(["message" => $message], 400);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $domainModel = new Domain();
            $companyModel = new Company();
            $cloudFlareService = new CloudFlareService();
            $haveEnterA = false;
            $domainId = current(Hashids::decode($id));

            if ($domainId) {
                //hash ok

                $domainProject = $domainModel->with(["project"])->find($domainId);

                $domain = $domainModel
                    ->with([
                        "domainsRecords" => function ($query) use ($domainProject) {
                            $query->where("system_flag", 0);
                            $query->orWhere(function ($queryWhere) use ($domainProject) {
                                $queryWhere->where("type", "A");
                                $queryWhere->where("name", $domainProject->name);
                            });
                        },
                    ])
                    ->find($domainId);

                if (Gate::allows("edit", [$domainProject->project])) {
                    //se tem permissao para editar o projeto, pode editar um dominio ligado a ele

                    $companies = $companyModel->all();

                    $registers = [];
                    foreach ($domain->domainsRecords as $record) {
                        if ($record->type == "A" && $record->name == $domain->name) {
                            $haveEnterA = true;
                        }

                        switch ($record->content) {
                            case $cloudFlareService::shopifyIp:
                                $content = $record->content;
                                //$content = "Servidores Shopify";
                                break;
                            case $cloudFlareService::checkoutIp:
                            case $cloudFlareService::adminIp:
                            case $cloudFlareService::sacIp:
                            case $cloudFlareService::affiliateIp:
                                $content = "Servidores CloudFox";
                                break;
                            default:
                                $content = $record->content;
                                break;
                        }

                        $newRegister = [
                            "id" => Hashids::encode($record->id),
                            "type" => $record->type,
                            "name" => $record->name,
                            "content" => $content,
                            "system_flag" => $record->system_flag,
                        ];

                        $registers[] = $newRegister;
                    }
                    if ($domain) {
                        return view("domains::edit", [
                            "domain" => $domain,
                            "companies" => $companies,
                            "registers" => $registers,
                            "project" => $domainProject->project,
                            "haveEnterA" => $haveEnterA,
                        ]);
                    } else {
                        return response()->json(["message" => "Domínio não encontrado"], 400);
                    }
                } else {
                    return response()->json(["message" => "Sem permissão para editar este domínio"], 400);
                }
            } else {
                //hash nao ok
                return response()->json(["message" => "Domínio não encontrado"], 400);
            }
        } catch (Exception $e) {
            $message = CloudflareErrorsService::formatErrorException($e);
            return response()->json(
                [
                    "message" => $message,
                ],
                400
            );
        }
    }

    public function destroy(DomainDestroyRequest $request): JsonResponse
    {
        try {
            $requestData = $request->validated();

            $domain = Domain::with("domainsRecords", "project", "project.shopifyIntegrations")->find(
                hashids_decode($requestData["domain"])
            );

            if (empty($domain)) {
                return response()->json(["message" => "Projeto não encontrado!"], 400);
            }

            if (empty($domain->project) && !Gate::allows("edit", [$domain->project])) {
                return response()->json(["message" => "Não foi possível deletar o domínio!"], 400);
            }

            $domainService = new DomainService();
            $deleteDomain = $domainService->deleteDomain($domain);

            if ($deleteDomain["success"]) {
                return response()->json(["message" => $deleteDomain["message"]], 200);
            }

            return response()->json(["message" => $deleteDomain["message"]], 400);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Server Error."], 400);
        }
    }

    public function recheckOnly($project, $domain): JsonResponse
    {
        try {
            $cloudFlareService = new CloudFlareService();

            $domain = Domain::with(["domainsRecords", "project", "project.shopifyIntegrations"])->find(
                hashids_decode($domain)
            );

            if (empty($domain)) {
                return response()->json(["message" => "Domínio não encontrado"], 404);
            }

            if (!Gate::allows("edit", [$domain->project])) {
                return response()->json(
                    [
                        "message" => "Sem permissão para validar domínio",
                    ],
                    403
                );
            }

            activity()
                ->on(new Domain())
                ->tap(function (Activity $activity) use ($domain) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = $domain->id;
                })
                ->log("Verificação domínio: " . $domain->name);

            if (!$cloudFlareService->checkHtmlMetadata("https://checkout." . $domain->name, "checkout-cloudfox", "1")) {
                $domain->update(["status" => Domain::STATUS_PENDING]);

                return response()->json(
                    [
                        "message" => "A verificação falhou, atualização de nameservers pendentes",
                    ],
                    400
                );
            }

            if (empty($domain->project->shopify_id)) {
                $domain->update(["status" => Domain::STATUS_APPROVED]);
                TaskService::setCompletedTask($domain->project->users->first(), Task::find(Task::TASK_DOMAIN_APPROVED));

                return response()->json(["message" => "Dominio revalidado com sucesso!"]);
            }

            if (empty($domain->project->shopifyIntegrations)) {
                $domain->update(["status" => Domain::STATUS_PENDING]);

                return response()->json(
                    [
                        "message" => "Não foi possivel revalidar o domínio, integração do shopify não encontrada",
                    ],
                    400
                );
            }

            $domain->update(["status" => Domain::STATUS_APPROVED]);
            TaskService::setCompletedTask($domain->project->users->first(), Task::find(Task::TASK_DOMAIN_APPROVED));

            $shopifyIntegration = $domain->project->shopifyIntegrations->first();

            try {
                $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token, false);
                $shopify->setThemeByRole("main");
            } catch (Exception $e) {
                report($e);

                return response()->json(
                    [
                        "message" => "Ocorreu um erro, irregularidade na loja shopify",
                    ],
                    400
                );
            }

            if (!empty($shopifyIntegration->layout_theme_html)) {
                return response()->json(
                    [
                        "message" => "Ocorreu um problema ao revalidar o domínio",
                    ],
                    400
                );
            }

            $htmlCart = null;
            $templateKeyName = null;
            foreach ($shopify::templateKeyNames as $template) {
                $templateKeyName = $template;
                $htmlCart = $shopify->getTemplateHtml($template);
                if ($htmlCart) {
                    break;
                }
            }

            try {
                if ($htmlCart) {
                    //template normal
                    if ($shopify->checkCartTemplate($htmlCart)) {
                        return response()->json(["message" => "Domínio validado com sucesso"], 200);
                    }

                    $shopify->setThemeByRole("main");
                    $htmlCart = $shopify->getTemplateHtml($templateKeyName);

                    $shopifyIntegration->update([
                        "theme_type" => ShopifyIntegration::SHOPIFY_BASIC_THEME,
                        "theme_name" => $shopify->getThemeName(),
                        "theme_file" => $templateKeyName,
                        "theme_html" => $htmlCart,
                    ]);

                    $shopify->updateTemplateHtml($templateKeyName, $htmlCart, $domain->name);

                    //Insert Tracking (src, utm)
                    $htmlBody = $shopify->getTemplateHtml("layout/theme.liquid");
                    if ($htmlBody) {
                        $shopifyIntegration->update(["layout_theme_html" => $htmlBody]);
                        $shopify->insertUtmTracking("layout/theme.liquid", $htmlBody);
                    }

                    return response()->json(["message" => "Domínio validado com sucesso"], 200);
                }

                //template ajax
                $htmlCart = $shopify->getTemplateHtml($shopify::templateAjaxKeyName);

                $shopifyIntegration->update([
                    "theme_type" => ShopifyIntegration::SHOPIFY_AJAX_THEME,
                    "theme_name" => $shopify->getThemeName(),
                    "theme_file" => $shopify::templateAjaxKeyName,
                    "theme_html" => $htmlCart,
                ]);

                if (!empty($htmlCart)) {
                    $shopify->updateTemplateHtml($templateKeyName, $htmlCart, $domain->name, true);
                }

                return response()->json(["message" => "Domínio validado com sucesso"], 200);
            } catch (Exception $e) {
                report($e);

                $domain->update(["status" => Domain::STATUS_PENDING]);

                return response()->json(
                    [
                        "message" => "Domínio validado com sucesso, mas a integração com o shopify não foi encontrada",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            $message = CloudflareErrorsService::formatErrorException($e);

            return response()->json(["message" => $message], 400);
        }
    }

    public function show($project, $domain): JsonResponse
    {
        try {
            $domainModel = new Domain();

            activity()
                ->on($domainModel)
                ->tap(function (Activity $activity) use ($domain) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = current(Hashids::decode($domain));
                })
                ->log("Visualizou tela verificação domínio: " . $domain);

            if (!empty($domain)) {
                $cloudFlareService = new CloudFlareService();

                $domain = $domainModel
                    ->with(["project"])
                    ->where("id", current(Hashids::decode($domain)))
                    ->first();

                if (Gate::allows("edit", [$domain->project])) {
                    $newNameServers = [];
                    $domainHost = " ";
                    foreach ($cloudFlareService->getZones() as $zone) {
                        if ($zone->name == $domain->name) {
                            foreach ($zone->name_servers as $new_name_server) {
                                $newNameServers[] = $new_name_server;
                            }
                            if ($zone->original_registrar != "") {
                                $domainHost = "(" . $zone->original_registrar . ")";
                            }
                        }
                    }

                    return response()->json(
                        [
                            "message" => "Dados do dominio",
                            "data" => [
                                "id_code" => Hashids::encode($domain->id),
                                "zones" => $newNameServers,
                                "domainHost" => $domainHost,
                                "status" => $domain->status,
                            ],
                        ],
                        200
                    );
                } else {
                    return response()->json(["message" => "Sem permissão para visualizar o domínio"], 400);
                }
            } else {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro, dominio nao encontrado",
                    ],
                    400
                );
            }
        } catch (Exception $e) {
            $message = CloudflareErrorsService::formatErrorException($e);

            return response()->json(
                [
                    "message" => $message,
                ],
                400
            );
        }
    }
}
