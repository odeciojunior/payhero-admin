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
            $domainModel       = new Domain();
            $companyModel      = new Company();
            $cloudFlareService = new CloudFlareService();
            $haveEnterA        = false;

            $domainId = current(Hashids::decode($id));

            if ($domainId) {
                //hash ok

                $domainProject = $domainModel->with(['project'])->find($domainId);

                $domain = $domainModel->with([
                                                 'domainsRecords' => function($query) use ($domainProject) {
                                                     $query->where('system_flag', 0);
                                                     $query->orWhere(function($queryWhere) use ($domainProject) {
                                                         $queryWhere->where('type', 'A');
                                                         $queryWhere->where('name', $domainProject->name);
                                                     });
                                                 },
                                             ])->find($domainId);

                if (Gate::allows('edit', [$domainProject->project])) {
                    //se tem permissao para editar o projeto, pode editar um dominio ligado a ele

                    $companies = $companyModel->all();

                    $registers = [];
                    foreach ($domain->domainsRecords as $record) {

                        if ($record->type == 'A' && $record->name == $domain->name) {
                            $haveEnterA = true;
                        }
                        $subdomain = explode('.', $record->name);

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
                            'domain'     => $domain,
                            'companies'  => $companies,
                            'registers'  => $registers,
                            'project'    => $domainProject->project,
                            'haveEnterA' => $haveEnterA,
                        ]);
                    } else {
                        return response()->json(['message' => 'Domínio não encontrado'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para editar este domínio'], 400);
                }
            } else {
                //hash nao ok
                return response()->json(['message' => 'Domínio não encontrado'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar Domínio (DomainsController - edit)');
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
            $domainModel       = new Domain();
            $domainRecordModel = new DomainRecord();
            $cloudFlareService = new CloudFlareService();

            DB::beginTransaction();

            $requestData = $request->all();

            $recordsJson = json_decode($requestData['data']);

            $domain = $domainModel->with(['domainsRecords', 'project'])
                                  ->find(current(Hashids::decode($requestData['domain'])));

            if (Gate::allows('edit', [$domain->project])) {

                if (!empty($recordsJson)) {
                    $cloudFlareService->setZone($domain->name);
                    foreach ($recordsJson as $records) {
                        foreach ($records as $record) {
                            $subdomain = current($record[1]);

                            if ($subdomain == '' || $subdomain == '@') {
                                $subdomain = $domain->name;
                            }

                            $subdomain = str_replace("http://", "", $subdomain);
                            $subdomain = str_replace("https://", "", $subdomain);
                            $subdomain = 'http://' . $subdomain;
                            $subdomain = parse_url($subdomain, PHP_URL_HOST);

                            if ((strpos($subdomain, '.') === false) ||
                                ($subdomain == $domain->name)) {
                                //dominio nao tem "ponto" ou é igual ao dominio

                                if ($domain->domainsRecords->where('type', current($record[0]))
                                                           ->where('name', $subdomain)
                                                           ->where('content', current($record[2]))
                                                           ->count() == 0) {
                                    //nao existe a record

                                    if (is_numeric(current($record[3]))) {
                                        $priority = current($record[3]);
                                    } else {
                                        $priority = 1;
                                    }

                                    if (current($record[0]) == 'MX') {
                                        $cloudRecordId = $cloudFlareService->addRecord(current($record[0]), $subdomain, current($record[2]), 0, false, $priority);
                                    } else if (current($record[0]) == 'TXT') {
                                        $cloudRecordId = $cloudFlareService->addRecord(current($record[0]), $subdomain, current($record[2]), 0, false);
                                        //                                    } else if ((current($record[0]) == 'A') && ($domain->name == $subdomain) && (!empty($domain->project->shopify_id))) {
                                        //                                        //dominio já será adicionado com o ip do shopify, nao permitir que seja inserido outro record "A"
                                        //                                        return response()->json(['message' => 'Erro ao tentar cadastrar subdomínio'], 400);
                                    } else {
                                        $cloudRecordId = $cloudFlareService->addRecord(current($record[0]), $subdomain, current($record[2]));
                                    }

                                    if (!empty($cloudRecordId)) {
                                        $newRecord = $domainRecordModel->create([
                                                                                    'domain_id'            => $domain->id,
                                                                                    'cloudflare_record_id' => $cloudRecordId,
                                                                                    'type'                 => current($record[0]),
                                                                                    'name'                 => $subdomain,
                                                                                    'content'              => current($record[2]),
                                                                                    'system_flag'          => 0,
                                                                                    'priority'             => $priority,
                                                                                ]);

                                        DB::commit();

                                        return response()->json(['message' => "Subdomínio cadastrado com sucesso"], 200);
                                    } else {
                                        //dominio já cadastrado
                                        DB::rollBack();

                                        return response()->json(['message' => 'Erro ao cadastrar domínios'], 400);
                                    }
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
                } else {
                    DB::rollBack();

                    return response()->json(['message' => 'Nenhum domínio adicionado.'], 400);
                }
            } else {
                DB::rollBack();

                return response()->json(['message' => 'Sem permissão para atualizar domínio'], 400);
            }
        } catch (Exception $e) {
            DB::rollBack();

            Log::warning('Erro ao tentar salvar dominio personalisado DomainsController - update');
            report($e);

            return response()->json(['message' => 'Erro ao atualizar domínios'], 400);
        }
    }

    /**
     * @param DomainDestroyRequest $request
     * @return JsonResponse
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

            $domain = $domainModel->with('domainsRecords', 'project', 'project.shopifyIntegrations')
                                  ->find($domainId);

            if (Gate::allows('edit', [$domain->project])) {

                if ($cloudFlareService->deleteZoneById($domain->cloudflare_domain_id) || empty($cloudFlareService->getZones($domain->name))) {
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
                            } catch (Exception $e) {
                                //throwl

                            }
                        }

                        return response()->json(['message' => 'Domínio removido com sucesso'], 200);
                    } else {
                        return response()->json(['message' => 'Não foi possível deletar o registro do domínio!'], 400);
                    }
                } else {
                    //erro ao deletar zona
                    return response()->json(['message' => 'Não foi possível deletar o domínio!'], 400);
                }
            } else {
                return response()->json(['message' => 'Sem permissão para deletar domínio'], 400);
            }
        } catch (Exception $e) {
            Log::warning('DomainsController destroy - erro ao deletar domínio');
            report($e);
        }
    }

    /**
     * @param $domainId
     * @return JsonResponse
     * @throws Throwable
     */
    public function show($domainId)
    {
        $domainModel       = new Domain();
        $cloudFlareService = new CloudFlareService();

        $domain = $domainModel->with(['project'])->where('id', Hashids::decode($domainId))->first();

        if (Gate::allows('edit', [$domain->project])) {

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
        } else {
            return response()->json(['message' => 'Sem permissão para visualizar domínio'], 400);
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

            $projectId = $requestData['project_id'] ?? null;
            $projectId = current(Hashids::decode($projectId));

            if ($projectId) {

                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {

                    $form = view('domains::create', [
                        'project' => $project,
                    ]);

                    return response()->json($form->render());
                } else {
                    return response()->json(['message' => 'Sem permissão para criar domínio'], 403);
                }
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
     * @return JsonResponse
     */
    public function recheckDomain(Request $request)
    {

        try {
            $domainService = new DomainService();
            $domainModel   = new Domain();

            $requestData = $request->all();
            $domainId    = current(Hashids::decode($requestData['domain']));
            if ($domainId) {
                $domain = $domainModel->with(['project'])->where('id', $domainId)->first();

                if (Gate::allows('edit', [$domain->project])) {

                    //hashid ok
                    if ($domainService->verifyPendingDomains($domainId, true)) {
                        return response()->json(['message' => 'Domínio verificado com sucesso'], 200);
                    } else {
                        return response()->json(['message' => 'Não foi possível verificar o domínio'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Sem permissão para validar o domínio'], 400);
                }
            } else {
                return response()->json(['message' => 'Não foi possível validar o domínio'], 400);
            }
        } catch (Exception $e) {
            Log::warning('DomainsController recheckDomain - erro ao revalidar o domínio');
            report($e);

            return response()->json(['message' => 'Problema ao validar o domínio'], 400);
        }
    }

    /**
     * @param intger $domainId
     * @return JsonResponse
     */
    public function getDomainData($domainId)
    {
        $domainModel       = new Domain();
        $cloudFlareService = new CloudFlareService();

        $domain = $domainModel->with(['project'])->where('id', current(Hashids::decode($domainId)))->first();

        if (Gate::allows('edit', [$domain->project])) {
            $newNameServers = [];
            $domainHost     = ' ';
            foreach ($cloudFlareService->getZones() as $zone) {
                if ($zone->name == $domain->name) {
                    foreach ($zone->name_servers as $new_name_server) {
                        $newNameServers[] = $new_name_server;
                    }
                    if ($zone->original_registrar != '') {
                        $domainHost = "(" . $zone->original_registrar . ")";
                    }
                }
            }

            return response()->json(['message' => 'Dados do domínio', 'data' => ['id_code' => Hashids::encode($domain->id), 'zones' => $newNameServers, 'domainHost' => $domainHost]], 200);
        } else {
            return response()->json(['message' => 'Sem permissão para visualizar o domínio'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    /*public function recheckOnly(Request $request)
    {
        try {
            $domainModel       = new Domain();
            $cloudFlareService = new CloudFlareService();

            $requestData = $request->all();
            $domainId    = current(Hashids::decode($requestData['domain']));
            if ($domainId) {
                //hashid ok
                $domain = $domainModel->with('records', 'project', 'project.shopifyIntegrations')
                                      ->find($domainId);

                if (Gate::allows('edit', [$domain->project])) {

                    if ($domain) {
                        //dominio existe

                        if ($cloudFlareService->checkHtmlMetadata('https://checkout.' . $domain->name, 'checkout-cloudfox', '1')) {

                            if (!empty($domain->project->shopify_id)) {
                                //se for shopify, fazer o check
                                try {

                                    if ($domain->project->shopifyIntegrations) {
                                        foreach ($domain->project->shopifyIntegrations as $shopifyIntegration) {
                                            $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                                            $shopify->setThemeByRole('main');
                                            if (!empty($shopifyIntegration->layout_theme_html)) {
                                                $html = $shopify->getTemplateHtml($shopify::templateKeyName);
                                                if ($shopify->checkCartTemplate($html)) {
                                                    $domain->update([
                                                                        'status' => $domainModel->getEnum('status', 'approved'),
                                                                    ]);

                                                    return response()->json(['message' => 'Domínio validado com sucesso'], 200);
                                                } else {

                                                    try {

                                                        $shopify->setThemeByRole('main');
                                                        $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');

                                                        if ($htmlCart) {
                                                            //template normal

                                                            $shopifyIntegration->update([
                                                                                            'theme_type' => $shopifyIntegration->getEnum('theme_type', 'basic_theme'),
                                                                                            'theme_name' => $shopify->getThemeName(),
                                                                                            'theme_file' => 'sections/cart-template.liquid',
                                                                                            'theme_html' => $htmlCart,
                                                                                        ]);

                                                            $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, $domain->name);
                                                        } else {

                                                            //template ajax
                                                            $shopifyIntegration->update([
                                                                                            'theme_type' => $this->getShopifyIntegrationModel()
                                                                                                                 ->getEnum('theme_type', 'ajax_theme'),
                                                                                            'theme_name' => $shopify->getThemeName(),
                                                                                            'theme_file' => 'snippets/ajax-cart-template.liquid',
                                                                                            'theme_html' => $htmlCart,
                                                                                        ]);

                                                            $shopify->updateTemplateHtml('snippets/ajax-cart-template.liquid', $htmlCart, $domain->name, true);
                                                        }

                                                        //inserir o javascript para o trackeamento (src, utm)
                                                        $htmlBody = $shopify->getTemplateHtml('layout/theme.liquid');
                                                        if ($htmlBody) {
                                                            //template do layout
                                                            $shopifyIntegration->update([
                                                                                            'layout_theme_html' => $htmlBody,
                                                                                        ]);

                                                            $shopify->insertUtmTracking('layout/theme.liquid', $htmlBody);
                                                        }
                                                    } catch (Exception $e) {
                                                        report($e);

                                                        $domain->update([
                                                                            'status' => $domainModel->getEnum('status', 'pending'),
                                                                        ]);

                                                        return response()->json(['message' => 'Domínio validado com sucesso, mas a integração com o shopify não foi encontrada'], 400);
                                                    }

                                                    $domain->update([
                                                                        'status' => $domainModel->getEnum('status', 'approved'),
                                                                    ]);

                                                    return response()->json(['message' => 'Domínio validado com sucesso'], 200);
                                                }
                                            }
                                        }
                                    } else {
                                        //integração nao encontrada
                                        $domain->update([
                                                            'status' => $domainModel->getEnum('status', 'pending'),
                                                        ]);

                                        return response()->json(['message' => 'Não foi possível revalidar o domínio, integração do shopify não encontrada'], 400);
                                    }
                                } catch (Exception $e) {
                                    //throwl

                                }
                            } else {
                                //nao eh integracao shopfy, validar dominio
                                $domain->update([
                                                    'status' => $domainModel->getEnum('status', 'approved'),
                                                ]);

                                return response()->json(['message' => 'Domínio revalidado com sucesso'], 200);
                            }
                            //return response()->json(['message' => 'Domínio revalidado com sucesso'], 200);
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
                    return response()->json(['message' => 'Sem permissão para validar domínio'], 400);
                }
            } else {
                //hash invalido
                return response()->json(['message' => 'Não foi possível revalidar o domínio'], 400);
            }
        } catch (Exception $e) {
            Log::warning('DomainsController recheckDomain - erro ao revalidar o domínio');
            report($e);

            return response()->json(['message' => 'Problema ao revalidar o domínio'], 400);
        }
    }*/
}

