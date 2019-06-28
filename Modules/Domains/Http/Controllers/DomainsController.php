<?php

namespace Modules\Domains\Http\Controllers;

use App\Entities\Company;
use Exception;
use App\Entities\Domain;
use App\Entities\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\User;
use Illuminate\Routing\Controller;
use Cloudflare\API\Endpoints\Zones;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\FoxUtils;
use Modules\Domains\Http\Requests\DomainCreateRequest;
use Modules\Domains\Http\Requests\DomainDestroyRequest;
use Modules\Domains\Http\Requests\DomainIndexRequest;
use Modules\Domains\Http\Requests\DomainStoreRequest;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\AutorizacaoHelper;
use Modules\Dominios\Transformers\DomainResource;

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
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $dataRequest = $request->all();
            if (isset($dataRequest["project"])) {
                $projectId = current(Hashids::decode($dataRequest["project"]));

                $project = $this->getProjectModel()->with('domains')->find($projectId);

                return DomainResource::collection($project->domains);
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
            $requestData = $request->validated();

            $projectId = $requestData['project_id'] ?? null;
            $projectId = current(Hashids::decode($projectId));

            if ($projectId) {

                $project = $this->getProjectModel()->find($projectId);

                if ($project->shopify_id == null) {
                    $newDomain = $this->getCloudFlareService()
                                      ->integrationWebsite($requestData['name'], $requestData['domain_ip']);
                } else {
                    $newDomain                = $this->getCloudFlareService()
                                                     ->integrationShopify($requestData['name']);
                    $requestData['domain_ip'] = 'Domínio Shopify';
                }

                if ($newDomain) {
                    $domainCreated = $this->getDomainModel()->create([
                                                                         'project_id' => $projectId,
                                                                         'name'       => $requestData['name'],
                                                                         'domain_ip'  => $requestData['domain_ip'],
                                                                         'status'     => $this->getDomainModel()
                                                                                              ->getEnum('status', 'pending'),
                                                                     ]);

                    //Domain::create($requestData);

                    return response()->json(['message' => 'Domínio cadastrado com sucesso'], 200);
                } else {
                    //problema ao cadastrar dominio
                    return response()->json(['message' => 'Erro ao configurar domínios.'], 400);
                }
            } else {
                //nao veio projectid

                return response()->json(['message' => 'Projeto não encontrado.'], 400);
            }
        } catch (Exception $e) {
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
            $domain    = $this->getDomainModel()->with(['project'])->find(current(Hashids::decode($id)));
            $companies = $this->getCompanyModel()->all();
            //$project   = $this->getProjectModel()->find($domain->project_id);

            $records = $this->getCloudFlareService()->getRecords($domain->name);

            $registros = [];
            foreach ($records as $record) {

                $novo_registro['id']   = $record->id;
                $novo_registro['tipo'] = $record->type;

                if ($record->name == $domain['name']) {
                    $novo_registro['nome'] = $record->name;
                } else {
                    $subdomain             = explode('.', $record->name);
                    $novo_registro['nome'] = $subdomain[0];
                }
                if ($record->content == "104.248.122.89")
                    $novo_registro['valor'] = "Servidores CloudFox";
                else
                    $novo_registro['valor'] = $record->content;

                if ($novo_registro['nome'] == 'checkout' || $novo_registro['nome'] == 'sac' || $novo_registro['nome'] == 'affiliate' || $novo_registro['nome'] == 'www' || $novo_registro['nome'] == $domain['name']) {
                    $novo_registro['deletar'] = false;
                } else {
                    $novo_registro['deletar'] = true;
                }

                $registros[] = $novo_registro;
            }
            if ($domain) {
                return view('domains::edit', [
                    'domain'    => $domain,
                    'companies' => $companies,
                    'registers' => $registros,
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

        $requestData = $request->all();

        $domain = Domain::find($requestData['id']);

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns     = new DNS($adapter);
        $zones   = new Zones($adapter);
        $zoneID  = $zones->getZoneID($domain['name']);

        for ($x = 1; $x < 10; $x++) {

            if (isset($requestData['tipo_registro_' . $x])) {

                try {
                    $dns->addRecord($zoneID, $requestData['tipo_registro_' . $x], $requestData['nome_registro_' . $x], $requestData['valor_registro_' . $x], 0, true);
                } catch (Exception $e) {
                    return response()->json('Não foi possível adicionar o novo registro DNS, verifique os dados informados !');
                }
            }
        }

        return response()->json('sucesso');
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

            $domain = $this->getDomainModel()->find($domainId);

            if ($this->getCloudFlareService()->deleteZone($domain->name)) {
                //zona deletada
                $domainDeleted = $domain->delete();

                if ($domainDeleted) {
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
        $domain = $this->getDomainModel()->where('id', Hashids::decode($domainId))->first();

        $key     = new APIKey(getenv('CLOUDFLARE_EMAIL'), getenv('CLOUDFLARE_TOKEN'));
        $adapter = new Guzzle($key);
        $user    = new User($adapter);
        $dns     = new DNS($adapter);
        $zones   = new Zones($adapter);

        $view = view('domains::show', [
            'domain' => $domain,
            'zones'  => $zones->listZones()->result,
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
     * @throws \Cloudflare\API\Endpoints\EndpointException
     */
    public function removerRegistroDns(Request $request)
    {

        return response()->json('Ocorreu algum erro ao remover o registro DNS');
        /*
        $requestData = $request->all();

        $domain = Domain::find($requestData['id_domain']);

        $key     = new APIKey('lorran_neverlost@hotmail.com', 'e8e1c0c37c306089f4791e8899846546f5f1d');
        $adapter = new Guzzle($key);
        $dns     = new DNS($adapter);
        $zones   = new Zones($adapter);
        $zoneID  = $zones->getZoneID($domain['name']);

        try {
            $dns->deleteRecord($zoneID, $requestData['id_registro']);
        } catch (\Exception $e) {
            return response()->json('Ocorreu algum erro ao remover o registro DNS');
        }

        return response()->json('sucesso');

        */
    }
}
