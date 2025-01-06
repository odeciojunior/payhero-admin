<?php

namespace Modules\Domains\Http\Controllers;

use Cloudflare\API\Endpoints\EndpointException;
use Cloudflare\API\Endpoints\SSL;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\DomainRecord;
use Modules\Core\Entities\Project;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\DomainService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\ShopifyService;
use Modules\Domains\Http\Requests\DomainCreateRequest;
use Modules\Domains\Http\Requests\DomainDestroyRecordRequest;
use Modules\Domains\Http\Requests\DomainDestroyRequest;
use Modules\Domains\Http\Requests\DomainStoreRequest;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Domains\Transformers\DomainResource;

/**
 * Class DomainsController
 * @package Modules\Domains\Http\Controllers
 */
class DomainsController extends Controller
{
    public function index()
    {
        //
    }

    public function store()
    {
        //
    }

    /**
     * @param Request $request
     * @param $id
     * @return Factory|View
     */
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
                        $subdomain = explode(".", $record->name);

                        switch ($record->content) {
                            case $cloudFlareService::shopifyIp:
                                $content = $record->content;
                                //$content = "Servidores Shopify";
                                break;
                            case $cloudFlareService::checkoutIp:
                            case $cloudFlareService::adminIp:
                            case $cloudFlareService::sacIp:
                            case $cloudFlareService::affiliateIp:
                                $content = "Servidores Azcend";
                                break;
                            default:
                                $content = $record->content;
                                break;
                        }

                        $newRegister = [
                            "id" => Hashids::encode($record->id),
                            "type" => $record->type,
                            //'name'        => ($record->name == $domain['name']) ? $record->name : ($subdomain[0] ?? ''),
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
            Log::warning("Erro ao tentar acessar tela editar Domínio (DomainsController - edit)");
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $domainModel = new Domain();
            $domainRecordModel = new DomainRecord();
            $cloudFlareService = new CloudFlareService();

            DB::beginTransaction();

            $requestData = $request->all();

            $recordsJson = json_decode($requestData["data"]);

            $domain = $domainModel
                ->with(["domainsRecords", "project"])
                ->find(current(Hashids::decode($requestData["domain"])));

            if (Gate::allows("edit", [$domain->project])) {
                if (!empty($recordsJson)) {
                    $cloudFlareService->setZone($domain->name);
                    foreach ($recordsJson as $records) {
                        foreach ($records as $record) {
                            $subdomain = current($record[1]);

                            if ($subdomain == "" || $subdomain == "@") {
                                $subdomain = $domain->name;
                            }

                            $subdomain = str_replace("http://", "", $subdomain);
                            $subdomain = str_replace("https://", "", $subdomain);
                            $subdomain = "http://" . $subdomain;
                            $subdomain = parse_url($subdomain, PHP_URL_HOST);

                            if (strpos($subdomain, ".") === false || $subdomain == $domain->name) {
                                //dominio nao tem "ponto" ou é igual ao dominio

                                if (
                                    $domain->domainsRecords
                                        ->where("type", current($record[0]))
                                        ->where("name", $subdomain)
                                        ->where("content", current($record[2]))
                                        ->count() == 0
                                ) {
                                    //nao existe a record

                                    if (is_numeric(current($record[3]))) {
                                        $priority = current($record[3]);
                                    } else {
                                        $priority = 1;
                                    }

                                    if (current($record[0]) == "MX") {
                                        $cloudRecordId = $cloudFlareService->addRecord(
                                            current($record[0]),
                                            $subdomain,
                                            current($record[2]),
                                            0,
                                            false,
                                            $priority
                                        );
                                    } elseif (current($record[0]) == "TXT") {
                                        $cloudRecordId = $cloudFlareService->addRecord(
                                            current($record[0]),
                                            $subdomain,
                                            current($record[2]),
                                            0,
                                            false
                                        );
                                        //                                    } else if ((current($record[0]) == 'A') && ($domain->name == $subdomain) && (!empty($domain->project->shopify_id))) {
                                        //                                        //dominio já será adicionado com o ip do shopify, nao permitir que seja inserido outro record "A"
                                        //                                        return response()->json(['message' => 'Erro ao tentar cadastrar subdomínio'], 400);
                                    } else {
                                        $cloudRecordId = $cloudFlareService->addRecord(
                                            current($record[0]),
                                            $subdomain,
                                            current($record[2])
                                        );
                                    }

                                    if (!empty($cloudRecordId)) {
                                        $newRecord = $domainRecordModel->create([
                                            "domain_id" => $domain->id,
                                            "cloudflare_record_id" => $cloudRecordId,
                                            "type" => current($record[0]),
                                            "name" => $subdomain,
                                            "content" => current($record[2]),
                                            "system_flag" => 0,
                                            "priority" => $priority,
                                        ]);

                                        DB::commit();

                                        return response()->json(
                                            ["message" => "Subdomínio cadastrado com sucesso"],
                                            200
                                        );
                                    } else {
                                        //dominio já cadastrado
                                        DB::rollBack();

                                        return response()->json(["message" => "Erro ao cadastrar domínios"], 400);
                                    }
                                } else {
                                    //dominio já cadastrado
                                    DB::rollBack();

                                    return response()->json(["message" => "Este domínio já esta cadastrado"], 400);
                                }
                            } else {
                                //dominio nao permitido
                                DB::rollBack();

                                return response()->json(["message" => "Domínio não permitido: " . $subdomain], 400);
                            }
                        }
                    }
                } else {
                    DB::rollBack();

                    return response()->json(["message" => "Nenhum domínio adicionado."], 400);
                }
            } else {
                DB::rollBack();

                return response()->json(["message" => "Sem permissão para atualizar domínio"], 400);
            }
        } catch (Exception $e) {
            DB::rollBack();

            Log::warning("Erro ao tentar salvar dominio personalisado DomainsController - update");
            report($e);

            return response()->json(["message" => "Erro ao atualizar domínios"], 400);
        }
    }

    /**
     * @param DomainDestroyRequest $request
     * @return JsonResponse
     */
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

    /**
     * @param $domainId
     * @return JsonResponse
     * @throws Throwable
     */
    public function show($domainId)
    {
        $domainModel = new Domain();
        $cloudFlareService = new CloudFlareService();

        $domain = $domainModel
            ->with(["project"])
            ->where("id", Hashids::decode($domainId))
            ->first();

        if (Gate::allows("edit", [$domain->project])) {
            $data = (object) [
                "id_code" => $domain->id_code,
                "name" => $domain->name,
                "domain_ip" => empty($domain->project->shopify_id) ? $domain->domain_ip : "Shopify",
            ];

            $view = view("domains::show", [
                "domain" => $data,
                "zones" => $cloudFlareService->getZones(),
            ]);

            return response()->json($view->render());
        } else {
            return response()->json(["message" => "Sem permissão para visualizar domínio"], 400);
        }
    }

    /**
     * @param DomainCreateRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function create(DomainCreateRequest $request)
    {
        try {
            $projectModel = new Project();

            $requestData = $request->validated();

            $projectId = $requestData["project_id"] ?? null;
            $projectId = current(Hashids::decode($projectId));

            if ($projectId) {
                $project = $projectModel->find($projectId);

                if (Gate::allows("edit", [$project])) {
                    $form = view("domains::create", [
                        "project" => $project,
                    ]);

                    return response()->json($form->render());
                } else {
                    return response()->json(["message" => "Sem permissão para criar domínio"], 403);
                }
            } else {
                //nao veio projectid

                return response()->json(["message" => "Projeto não encontrado."], 400);
            }
        } catch (Exception $e) {
            Log::warning("Erro ao obter form de cadastro de domínios (DomainsController - create)");
            report($e);

            return response()->json(["message" => __('messages.unexpected_error')], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function recheckDomain(Request $request)
    {
        try {
            $domainService = new DomainService();
            $domainModel = new Domain();

            $requestData = $request->all();
            $domainId = current(Hashids::decode($requestData["domain"]));
            if ($domainId) {
                $domain = $domainModel
                    ->with(["project"])
                    ->where("id", $domainId)
                    ->first();

                if (Gate::allows("edit", [$domain->project])) {
                    //hashid ok
                    if ($domainService->verifyPendingDomains($domainId, true)) {
                        return response()->json(["message" => "Domínio verificado com sucesso"], 200);
                    } else {
                        return response()->json(["message" => "Não foi possível verificar o domínio"], 400);
                    }
                } else {
                    return response()->json(["message" => "Sem permissão para validar o domínio"], 400);
                }
            } else {
                return response()->json(["message" => "Não foi possível validar o domínio"], 400);
            }
        } catch (Exception $e) {
            Log::warning("DomainsController recheckDomain - erro ao revalidar o domínio");
            report($e);

            return response()->json(["message" => "Problema ao validar o domínio"], 400);
        }
    }

    /**
     * @param intger $domainId
     * @return JsonResponse
     */
    public function getDomainData($domainId)
    {
        $domainModel = new Domain();
        $cloudFlareService = new CloudFlareService();

        $domain = $domainModel
            ->with(["project"])
            ->where("id", current(Hashids::decode($domainId)))
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
                    "message" => "Dados do domínio",
                    "data" => [
                        "id_code" => Hashids::encode($domain->id),
                        "zones" => $newNameServers,
                        "domainHost" => $domainHost,
                    ],
                ],
                200
            );
        } else {
            return response()->json(["message" => "Sem permissão para visualizar o domínio"], 400);
        }
    }
}
