<?php

namespace Modules\Domains\Http\Controllers;

use App\Entities\Company;
use App\Entities\DomainRecord;
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
     * @var Domain
     */
    private $domainModel;
    /**
     * @var Project
     */
    private $projectModel;
    /**
     * @var Company
     */
    private $companyModel;
    /**
     * @var CloudFlareService
     */
    private $cloudFlareService;
    /**
     * @var $domainRecordModel
     */
    private $domainRecordModel;
    /**
     * @var SendgridService
     */
    private $sendgridService;
    /**
     * @var ShopifyService
     */
    private $shopifyService;
    /**
     * @var DomainService
     */
    private $domainService;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|DomainService
     */
    private function getDomainService()
    {
        if (!$this->domainService) {
            $this->domainService = app(DomainService::class);
        }

        return $this->domainService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|SendgridService
     */
    private function getSendgridService()
    {
        if (!$this->sendgridService) {
            $this->sendgridService = app(SendgridService::class);
        }

        return $this->sendgridService;
    }

    /**
     * @return Domain|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDomainModel()
    {
        if (!$this->domainModel) {
            $this->domainModel = app(Domain::class);
        }

        return $this->domainModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDomainRecordModel()
    {
        if (!$this->domainRecordModel) {
            $this->domainRecordModel = app(DomainRecord::class);
        }

        return $this->domainRecordModel;
    }

    /**
     * @return Project|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getProjectModel()
    {
        if (!$this->projectModel) {
            $this->projectModel = app(Project::class);
        }

        return $this->projectModel;
    }

    /**
     * @return Company|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCompanyModel()
    {
        if (!$this->companyModel) {
            $this->companyModel = app(Company::class);
        }

        return $this->companyModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|CloudFlareService
     */
    private function getCloudFlareService()
    {
        if (!$this->cloudFlareService) {
            $this->cloudFlareService = app(CloudFlareService::class);
        }

        return $this->cloudFlareService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|ShopifyService
     */
    private function getShopifyService(string $urlStore = null, string $token = null)
    {
        if (!$this->shopifyService) {
            $this->shopifyService = new ShopifyService($urlStore, $token);
        }

        return $this->shopifyService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $dataRequest = $request->all();
            if (isset($dataRequest["project"])) {
                $projectId = current(Hashids::decode($dataRequest["project"]));

                $domains = $this->getDomainModel()->with(['project'])->where('project_id', $projectId)->get();

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
            DB::beginTransaction();
            $requestData = $request->validated();

            $projectId = $requestData['project_id'] ?? null;
            $projectId = current(Hashids::decode($projectId));

            if ($projectId) {

                $project = $this->getProjectModel()->find($projectId);

                if (!empty($project->shopify_id)) {
                    //projeto shopify
                    $domainIp = $this->getCloudFlareService()::shopifyIp;
                } else {
                    //projeto web
                    $domainIp = $requestData['domain_ip'];
                }

                $domainCreated = $this->getDomainModel()->create([
                                                                     'project_id' => $projectId,
                                                                     'name'       => $requestData['name'],
                                                                     'domain_ip'  => $domainIp,
                                                                     'status'     => $this->getDomainModel()
                                                                                          ->getEnum('status', 'pending'),
                                                                 ]);

                if ($domainCreated) {
                    if ($project->shopify_id == null) {
                        $newDomain = $this->getCloudFlareService()
                                          ->integrationWebsite($domainCreated->id, $requestData['name'], $domainIp);
                    } else {
                        $newDomain                = $this->getCloudFlareService()
                                                         ->integrationShopify($domainCreated->id, $requestData['name']);
                        $requestData['domain_ip'] = 'Domínio Shopify';
                    }

                    if ($newDomain) {
                        DB::commit();

                        return response()->json(['message' => 'Domínio cadastrado com sucesso'], 200);
                    } else {
                        //problema ao cadastrar dominio
                        dd($newDomain);
                        DB::rollBack();

                        return response()->json(['message' => 'Erro ao configurar domínios.'], 400);
                    }
                } else {
                    dd($domainCreated);
                    DB::rollBack();

                    return response()->json(['message' => 'Erro ao configurar domínios.'], 400);
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
            $domain    = $this->getDomainModel()->with(['project', 'records'])->find(current(Hashids::decode($id)));
            $companies = $this->getCompanyModel()->all();

            $registers = [];
            foreach ($domain->records as $record) {

                $subdomain = explode('.', $record->name);

                switch ($record->content) {
                    CASE $this->getCloudFlareService()::shopifyIp:
                        $content = "Servidores Shopify";
                        break;
                    CASE $this->getCloudFlareService()::checkoutIp:
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
            DB::beginTransaction();

            $requestData = $request->all();
            $recordsJson = json_decode($requestData['data']);

            $domain = $this->getDomainModel()->find(current(Hashids::decode($requestData['domain'])));

            $this->getCloudFlareService()->setZone($domain->name);
            foreach ($recordsJson as $records) {
                foreach ($records as $record) {
                    if (!$this->getDomainRecordModel()
                              ->where('type', current($record[0]))
                              ->where('name', current($record[1]))
                              ->where('content', current($record[2]))
                              ->exists()) {
                        //nao existe a record
                        $this->getCloudFlareService()
                             ->addRecord(current($record[0]), current($record[1]), current($record[2]));
                        $newRecord = $this->getDomainRecordModel()->create([
                                                                               'domain_id'   => $domain->id,
                                                                               'type'        => current($record[0]),
                                                                               'name'        => current($record[1]),
                                                                               'content'     => current($record[2]),
                                                                               'system_flag' => 0,
                                                                           ]);
                    } else {
                        //dominio já cadastrado
                        DB::rollBack();

                        return response()->json(['message' => 'Este dominio já esta cadastrado'], 400);
                    }
                }
            }

            DB::commit();

            return response()->json(['message' => "Dominio atualizado com sucesso"], 200);
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
            $requestData = $request->validated();

            $domainId = current(Hashids::decode($requestData['id']));

            $domain = $this->getDomainModel()->with('records', 'project', 'project.shopifyIntegrations')
                           ->find($domainId);

            if ($this->getCloudFlareService()->deleteZone($domain->name)) {
                //zona deletada
                $this->getSendgridService()->deleteLinkBrand($domain->name);
                $this->getSendgridService()->deleteZone($domain->name);

                $recordsDeleted = $this->getDomainRecordModel()->where('domain_id', $domain->id)->delete();
                $domainDeleted  = $domain->delete();

                if ($domainDeleted) {

                    if (!empty($domain->project->shopify_id)) {
                        //se for shopify, voltar as integraçoes ao html padrao
                        try {

                            foreach ($domain->project->shopifyIntegrations as $shopifyIntegration) {
                                $shopify = $this->getShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                                $shopify->setThemeByRole('main');
                                $shopify->setTemplateHtml($shopifyIntegration->theme_file, $shopifyIntegration->theme_html);
                                $shopify->setTemplateHtml('layout/theme.liquid', $shopifyIntegration->layout_theme_html);
                                $shopifyIntegration->delete();
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
        $domain = $this->getDomainModel()->with(['project'])->where('id', Hashids::decode($domainId))->first();

        $data = (object) [
            'id_code'   => $domain->id_code,
            'name'      => $domain->name,
            'domain_ip' => (empty($domain->project->shopify_id)) ? $domain->domain_ip : 'Shopify',
        ];

        $view = view('domains::show', [
            'domain' => $data,
            'zones'  => $this->getCloudFlareService()->getZones(),
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

            $requestData = $request->validated();

            $projectId = $requestData['project_id'] ?? null;
            $projectId = current(Hashids::decode($projectId));

            if ($projectId) {

                $project = $this->getProjectModel()->find($projectId);

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
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyRecord(DomainDestroyRecordRequest $request)
    {
        try {
            $requestData = $request->validated();

            $recordId = current(Hashids::decode($requestData['id_record']));
            $record   = $this->getDomainRecordModel()->with('domain')->find($recordId);

            $this->getCloudFlareService()->setZone($record->domain->name);
            if ($this->getCloudFlareService()->deleteRecord($record->name . '.' . $record->domain->name)) {
                //zona deletada

                $recordsDeleted = $this->getDomainRecordModel()->where('id', $record->id)->delete();

                if ($recordsDeleted) {
                    return response()->json(['message' => 'Dns removido com sucesso'], 200);
                } else {
                    return response()->json(['message' => 'Não foi possível deletar o dns!'], 400);
                }
            } else {
                //erro ao deletar zona
                return response()->json(['message' => 'Não foi possível deletar o dns!'], 400);
            }
        } catch (Exception $e) {
            Log::warning('DomainsController destroyRecord - erro ao deletar dns');
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
            $requestData = $request->all();
            $domainId    = current(Hashids::decode($requestData['domain']));
            if ($domainId) {
                //hashid ok
                if ($this->getDomainService()->verifyPendingDomains($domainId, true)) {
                    return response()->json(['message' => 'Dns revalidado com sucesso'], 200);
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
}
