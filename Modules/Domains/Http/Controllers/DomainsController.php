<?php

namespace Modules\Domains\Http\Controllers;

use App\Entities\Company;
use App\Entities\DomainRecord;
use Cloudflare\API\Endpoints\SSL;
use Exception;
use App\Entities\Domain;
use App\Entities\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\DomainService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\ShopifyService;
use Modules\Domains\Http\Requests\DomainCreateRequest;
use Modules\Domains\Http\Requests\DomainDestroyRecordRequest;
use Modules\Domains\Http\Requests\DomainDestroyRequest;
use Modules\Domains\Http\Requests\DomainStoreRequest;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Domains\Transformers\DomainResource;

/**
 * Class DomainsController
 * @package Modules\Domains\Http\Controllers
 */
class DomainsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $domainModel = new Domain();

            $dataRequest = $request->all();
            if (isset($dataRequest["project"])) {
                $projectId = current(Hashids::decode($dataRequest["project"]));

                $domains = $domainModel->with(['project'])->where('project_id', $projectId)->get();

                return DomainResource::collection($domains);
            } else {
                return response()->json([
                                            'message' => 'Erro ao listar dados de domínios',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados (DomainsController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao listar dados de domínios',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DomainStoreRequest $request)
    {
        try {
            $domainModel       = new Domain();
            $projectModel      = new Project();
            $cloudFlareService = new CloudFlareService();

            DB::beginTransaction();
            $requestData = $request->validated();

            $projectId = $requestData['project_id'] ?? null;
            $projectId = current(Hashids::decode($projectId));

            if ($projectId) {

                $project = $projectModel->with(['domains'])->find($projectId);

                if (!empty($project->shopify_id)) {
                    //projeto shopify
                    $domainIp = $cloudFlareService::shopifyIp;
                } else {
                    //projeto web
                    $domainIp = null;
                }

                //tratamento parcial do dominio
                $requestData['name'] = str_replace("http://", "", $requestData['name']);
                $requestData['name'] = str_replace("https://", "", $requestData['name']);
                $requestData['name'] = str_replace("www.", "", $requestData['name']);
                $requestData['name'] = 'http://' . $requestData['name'];
                $requestData['name'] = parse_url($requestData['name'], PHP_URL_HOST);

                if ($project->domains->where('name', $requestData['name'])
                                     ->count() == 0) {

                    if (empty($cloudFlareService->getZones($requestData['name']))) {
                        $domainCreated = $domainModel->create([
                                                                  'project_id' => $projectId,
                                                                  'name'       => $requestData['name'],
                                                                  'domain_ip'  => $domainIp,
                                                                  'status'     => $domainModel->getEnum('status', 'pending'),
                                                              ]);

                        if ($domainCreated) {
                            if ($project->shopify_id == null) {
                                $newDomain = $cloudFlareService->integrationWebsite($domainCreated->id, $requestData['name'], $domainIp);
                            } else {
                                $newDomain                = $cloudFlareService->integrationShopify($domainCreated->id, $requestData['name']);
                                $requestData['domain_ip'] = 'Domínio Shopify';
                            }

                            $cloudFlareService->setCloudFlareConfig($requestData['name']);

                            if ($newDomain) {
                                DB::commit();

                                $newNameServers = [];
                                foreach ($cloudFlareService->getZones() as $zone) {
                                    if ($zone->name == $domainCreated->name) {
                                        foreach ($zone->name_servers as $new_name_server) {
                                            $newNameServers[] = $new_name_server;
                                        }
                                    }
                                }

                                return response()->json(['message' => 'Domínio cadastrado com sucesso', 'data' => ['id_code' => Hashids::encode($domainCreated->id), 'zones' => $newNameServers]], 200);
                            } else {
                                //problema ao cadastrar dominio
                                DB::rollBack();

                                return response()->json(['message' => 'Erro ao configurar domínios.'], 400);
                            }
                        } else {
                            //erro ao criar dominio
                            DB::rollBack();

                            return response()->json(['message' => 'Erro ao configurar domínios.'], 400);
                        }
                    } else {
                        //dominio ja existe registrado no cloudflare
                        DB::rollBack();

                        return response()->json(['message' => 'Domínio já está sendo utilizado'], 400);
                    }
                } else {
                    //dominio ja cadastrado

                    DB::rollBack();

                    return response()->json(['message' => 'Domínios já cadastrado.'], 400);
                }
            } else {
                //nao veio projectid
                DB::rollBack();

                return response()->json(['message' => 'Projeto não encontrado.'], 400);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::warning('Erro ao obter form de cadastro de domínios (DomainsController - create)');
            report($e);

            return response()->json(['message' => 'Erro ao configurar domínios.'], 400);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $domainModel       = new Domain();
            $companyModel      = new Company();
            $cloudFlareService = new CloudFlareService();

            $domain = $domainModel->with([
                                             'project',
                                             'records' => function($query) {
                                                 $query->where('system_flag', 0);
                                             },
                                         ])->find(current(Hashids::decode($id)));

            $companies = $companyModel->all();

            $registers = [];
            foreach ($domain->records as $record) {

                $subdomain = explode('.', $record->name);

                switch ($record->content) {
                    CASE $cloudFlareService::shopifyIp:
                        $content = "Servidores Shopify";
                        break;
                    CASE $cloudFlareService::checkoutIp:
                        $content = "Servidores CloudFox";
                        break;
                    default:
                        $content = $record->content;
                        break;
                }

                $newRegister = [
                    'id'          => Hashids::encode($record->id),
                    'type'        => $record->type,
                    //'name'        => ($record->name == $domain['name']) ? $record->name : ($subdomain[0] ?? ''),
                    'name'        => $record->name,
                    'content'     => $content,
                    'system_flag' => $record->system_flag,

                ];

                $registers[] = $newRegister;
            }
            if ($domain) {
                return view('domains::edit', [
                    'domain'    => $domain,
                    'companies' => $companies,
                    'registers' => $registers,
                    'project'   => $domain->project,
                ]);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar Domínio (DomainsController - edit)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function update(Request $request)
    {
        try {
            $domainModel       = new Domain();
            $domainRecordModel = new DomainRecord();
            $cloudFlareService = new CloudFlareService();

            DB::beginTransaction();

            $requestData = $request->all();
            $recordsJson = json_decode($requestData['data']);

            $domain = $domainModel->with(['records'])->find(current(Hashids::decode($requestData['domain'])));

            $cloudFlareService->setZone($domain->name);
            foreach ($recordsJson as $records) {
                foreach ($records as $record) {
                    $subdomain = current($record[1]);

                    if (($subdomain != '') || ($subdomain != '@')) {
                        $subdomain = str_replace("http://", "", $subdomain);
                        $subdomain = str_replace("https://", "", $subdomain);
                        $subdomain = 'http://' . $subdomain;
                        $subdomain = parse_url($subdomain, PHP_URL_HOST);
                    }

                    if ((strpos($subdomain, '.') === false) ||
                        ($subdomain == $domain->name)) {
                        //dominio nao tem "ponto" ou é igual ao dominio

                        if ($domain->records->where('type', current($record[0]))
                                            ->where('name', $subdomain)
                                            ->where('content', current($record[2]))
                                            ->count() == 0) {
                            //nao existe a record

                            $quantityMx = $domain->records->where('type', 'MX')->count();

                            if (current($record[0]) == 'MX') {
                                $cloudFlareService->addRecord(current($record[0]), $subdomain, current($record[2]), 0, false, $quantityMx + 1);
                            } else {
                                $cloudFlareService->addRecord(current($record[0]), $subdomain, current($record[2]));
                            }
                            $newRecord = $domainRecordModel->create([
                                                                        'domain_id'   => $domain->id,
                                                                        'type'        => current($record[0]),
                                                                        'name'        => $subdomain,
                                                                        'content'     => current($record[2]),
                                                                        'system_flag' => 0,
                                                                    ]);
                        } else {
                            //dominio já cadastrado
                            DB::rollBack();

                            return response()->json(['message' => 'Este domínio já esta cadastrado'], 400);
                        }
                    } else {
                        //dominio nao permitido
                        DB::rollBack();

                        return response()->json(['message' => 'Domínio não permitido: ' . $subdomain], 400);
                    }
                }
            }

            DB::commit();

            return response()->json(['message' => "Domínio atualizado com sucesso"], 200);
        } catch (Exception $e) {
            DB::rollBack();

            Log::warning('Erro ao tentar salvar dominio personalisado DomainsController - update');
            report($e);

            return response()->json(['message' => 'Erro ao atualizar dominios'], 400);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DomainDestroyRequest $request)
    {
        try {
            $domainModel       = new Domain();
            $domainRecordModel = new DomainRecord();
            $sendgridService   = new SendgridService();
            $cloudFlareService = new CloudFlareService();

            $requestData = $request->validated();

            $domainId = current(Hashids::decode($requestData['id']));

            $domain = $domainModel->with('records', 'project', 'project.shopifyIntegrations')
                                  ->find($domainId);

            if ($cloudFlareService->deleteZone($domain->name) || empty($cloudFlareService->getZones($domain->name))) {
                //zona deletada
                $sendgridService->deleteLinkBrand($domain->name);
                $sendgridService->deleteZone($domain->name);

                $recordsDeleted = $domainRecordModel->where('domain_id', $domain->id)->delete();
                $domainDeleted  = $domain->delete();

                if ($domainDeleted) {

                    if (!empty($domain->project->shopify_id)) {
                        //se for shopify, voltar as integraçoes ao html padrao
                        try {

                            foreach ($domain->project->shopifyIntegrations as $shopifyIntegration) {
                                $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                                $shopify->setThemeByRole('main');
                                if (!empty($shopifyIntegration->theme_html)) {
                                    $shopify->setTemplateHtml($shopifyIntegration->theme_file, $shopifyIntegration->theme_html);
                                }
                                if (!empty($shopifyIntegration->layout_theme_html)) {
                                    $shopify->setTemplateHtml('layout/theme.liquid', $shopifyIntegration->layout_theme_html);
                                }
                            }
                        } catch (\Exception $e) {
                            //throwl

                        }
                    }

                    return response()->json(['message' => 'Domínio removido com sucesso'], 200);
                } else {
                    return response()->json(['message' => 'Não foi possível deletar o domínio!'], 400);
                }
            } else {
                //erro ao deletar zona
                return response()->json(['message' => 'Não foi possível deletar o domínio!'], 400);
            }
        } catch (Exception $e) {
            Log::warning('DomainsController destroy - erro ao deletar domínio');
            report($e);
        }
    }

    /**
     * @param $domainId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function show($domainId)
    {
        $domainModel       = new Domain();
        $cloudFlareService = new CloudFlareService();

        $domain = $domainModel->with(['project'])->where('id', Hashids::decode($domainId))->first();

        $data = (object) [
            'id_code'   => $domain->id_code,
            'name'      => $domain->name,
            'domain_ip' => (empty($domain->project->shopify_id)) ? $domain->domain_ip : 'Shopify',
        ];

        $view = view('domains::show', [
            'domain' => $data,
            'zones'  => $cloudFlareService->getZones(),
        ]);

        return response()->json($view->render());
    }

    /**
     * @param DomainCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(DomainCreateRequest $request)
    {
        try {
            $prejectModel = new Project();

            $requestData = $request->validated();

            $projectId = $requestData['project_id'] ?? null;
            $projectId = current(Hashids::decode($projectId));

            if ($projectId) {

                $project = $prejectModel->find($projectId);

                $form = view('domains::create', [
                    'project' => $project,
                ]);

                return response()->json($form->render());
            } else {
                //nao veio projectid

                return response()->json(['message' => 'Projeto não encontrado.'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao obter form de cadastro de domínios (DomainsController - create)');
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyRecord(DomainDestroyRecordRequest $request)
    {
        try {
            $domainRecordModel = new DomainRecord();
            $cloudFlareService = new CloudFlareService();

            $requestData = $request->validated();

            $recordId = current(Hashids::decode($requestData['id_record']));
            $record   = $domainRecordModel->with('domain')->find($recordId);

            $cloudFlareService->setZone($record->domain->name);

            $recordName = '';
            if (str_contains($record->name, '.')) {
                $recordName = $record->name;
            } else {
                $recordName = $record->name . '.' . $record->domain->name;
            }

            if ($cloudFlareService->deleteRecord($recordName)) {

                //zona deletada
                $recordsDeleted = $domainRecordModel->where('id', $record->id)->delete();

                if ($recordsDeleted) {
                    return response()->json(['message' => 'Domínio removido com sucesso'], 200);
                } else {
                    return response()->json(['message' => 'Não foi possível deletar o domínio!'], 400);
                }
            } else {
                //erro ao deletar zona
                return response()->json(['message' => 'Não foi possível deletar o domínio!'], 400);
            }
        } catch (Exception $e) {
            Log::warning('DomainsController destroyRecord - erro ao deletar domínio');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recheckDomain(Request $request)
    {
        try {
            $domainService = new DomainService();

            $requestData = $request->all();
            $domainId    = current(Hashids::decode($requestData['domain']));
            if ($domainId) {
                //hashid ok
                if ($domainService->verifyPendingDomains($domainId, true)) {
                    return response()->json(['message' => 'Domínio revalidado com sucesso'], 200);
                } else {
                    return response()->json(['message' => 'Não foi possível revalidar o domínio'], 400);
                }
            } else {
                return response()->json(['message' => 'Não foi possível revalidar o domínio'], 400);
            }
        } catch (Exception $e) {
            Log::warning('DomainsController recheckDomain - erro ao revalidar o domínio');
            report($e);

            return response()->json(['message' => 'Não foi possível revalidar o domínio'], 400);
        }
    }

    /**
     * @param intger $domainId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDomainData($domainId)
    {
        $domainModel       = new Domain();
        $cloudFlareService = new CloudFlareService();

        $domain = $domainModel->with(['project'])->where('id', current(Hashids::decode($domainId)))->first();

        $newNameServers = [];
        foreach ($cloudFlareService->getZones() as $zone) {
            if ($zone->name == $domain->name) {
                foreach ($zone->name_servers as $new_name_server) {
                    $newNameServers[] = $new_name_server;
                }
            }
        }

        return response()->json(['message' => 'Dados do dominio', 'data' => ['id_code' => Hashids::encode($domain->id), 'zones' => $newNameServers]], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recheckOnly(Request $request)
    {
        try {
            $domainModel       = new Domain();
            $cloudFlareService = new CloudFlareService();

            $requestData = $request->all();
            $domainId    = current(Hashids::decode($requestData['domain']));
            if ($domainId) {
                //hashid ok
                $domain = $domainModel->find($domainId);
                if ($domain) {
                    //dominio existe

                    if ($cloudFlareService->checkHtmlMetadata('https://checkout.' . $domain->name, 'checkout-cloudfox', '1')) {
                        $domain->update([
                                            'status' => $domainModel->getEnum('status', 'approved'),
                                        ]);

                        return response()->json(['message' => 'Domínio revalidado com sucesso'], 200);
                    } else {
                        $domain->update([
                                            'status' => $domainModel->getEnum('status', 'pending'),
                                        ]);

                        return response()->json(['message' => 'Não foi possível revalidar o domínio'], 400);
                    }
                } else {
                    //dominio nao existe
                    return response()->json(['message' => 'Domínio não encontrado'], 400);
                }
            } else {
                //hash invalido
                return response()->json(['message' => 'Não foi possível revalidar o domínio'], 400);
            }
        } catch (Exception $e) {
            Log::warning('DomainsController recheckDomain - erro ao revalidar o domínio');
            report($e);

            return response()->json(['message' => 'Não foi possível revalidar o domínio'], 400);
        }
    }
}

