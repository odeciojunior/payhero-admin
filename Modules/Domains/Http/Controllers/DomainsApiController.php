<?php

namespace Modules\Domains\Http\Controllers;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\Zones;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\DomainRecord;
use Modules\Core\Entities\Project;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\ShopifyService;
use Modules\Domains\Http\Requests\DomainDestroyRequest;
use Modules\Domains\Http\Requests\DomainStoreRequest;
use Modules\Domains\Transformers\DomainResource;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;

/**
 * Class DomainsApiController
 * @package Modules\Dominios\Http\Controllers
 */
class DomainsApiController extends Controller
{
    /**
     * @param $projectId
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index($projectId)
    {
        try {
            $domainModel  = new Domain();
            $projectModel = new Project();

            if (!empty($projectId)) {

                $projectId = current(Hashids::decode($projectId));
                $project   = $projectModel->find($projectId);
                if (Gate::allows('index', [$project])) {
                    $domains = $domainModel->with('project')
                                           ->where('project_id', $projectId);

                    return DomainResource::collection($domains->orderBy('id', 'DESC')->paginate(5));
                } else {
                    return response()->json([
                                                'message' => 'Sem permissão para visualizar os domínios',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Erro ao listar dados de domínios',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados (DomainsApiController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao listar dados de domínios',
                                    ], 400);
        }
    }

    /**
     * @param DomainStoreRequest $request
     * @return JsonResponse
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

                if (Gate::allows('edit', [$project])) {
                    //se pode editar o projeto, pode editar os dominios

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
                                                                      'status'     => $domainModel->present()
                                                                                                  ->getStatus('pending'),
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

                                    return response()->json([
                                                                'message' => 'Domínio cadastrado com sucesso',
                                                                'data'    => ['id_code' => Hashids::encode($domainCreated->id), 'zones' => $newNameServers],
                                                            ], 200);
                                } else {
                                    //problema ao cadastrar dominio
                                    DB::rollBack();

                                    return response()->json(['message' => 'Erro ao criar domínio.'], 400);
                                }
                            } else {
                                //erro ao criar dominio
                                DB::rollBack();

                                return response()->json(['message' => 'Erro ao cadastrar domínio.'], 400);
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
                    DB::rollBack();

                    return response()->json(['message' => 'Sem permissão para criar um domínio neste projeto'], 403);
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
                            CASE $cloudFlareService::shopifyIp:
                                $content = $record->content;
                                //$content = "Servidores Shopify";
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

            return response()->json([
                                        'message' => 'Ocorreu  um erro, tente novamente mais tarde',
                                    ], 400);
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

            $domainId = current(Hashids::decode($requestData['domain']));

            $domain = $domainModel->with('domainsRecords', 'project', 'project.shopifyIntegrations')
                                  ->find($domainId);

            if (Gate::allows('edit', [$domain->project])) {
                if (!empty($domain->cloudflare_domain_id)) {
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
                                    report($e);

                                    return response()->json(['message' => 'Não foi possível deletar o registro do domínio!'], 400);
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

                    $recordsDeleted = $domainRecordModel->where('domain_id', $domain->id)->delete();
                    $domainDeleted  = $domain->delete();

                    if ($domainDeleted) {
                        return response()->json([
                                                    'message' => 'Dominio removido com sucesso!',
                                                ], 200);
                    } else {
                        return response()->json(['message' => 'Sem permissão para deletar domínio'], 400);
                    }
                }
            } else {
                return response()->json(['message' => 'Sem permissão para deletar domínio'], 400);
            }
        } catch (Exception $e) {
            Log::warning('DomainsApiController destroy - erro ao deletar domínio');
            report($e);

            //erro ao deletar dominio
            return response()->json(['message' => 'Não foi possível deletar o domínio!'], 400);
        }
    }

    /**
     * @param $project
     * @param $domain
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function recheckOnly($project, $domain)
    {
        try {
            $domainModel       = new Domain();
            $cloudFlareService = new CloudFlareService();

            $domainId = current(Hashids::decode($domain));
            if (!empty($domainId)) {
                // hashid ok
                $domain = $domainModel->with(['domainsRecords', 'project', 'project.shopifyIntegrations'])
                                      ->find($domainId);
                if (!empty($domain)) {

                    if (Gate::allows('edit', [$domain->project])) {
                        if ($cloudFlareService->checkHtmlMetadata('https://checkout.' . $domain->name, 'checkout-cloudfox', '1')) {
                            if (!empty($domain->project->shopify_id)) {
                                // se for shopify, fazer check

                                try {
                                    if (!empty($domain->project->shopifyIntegrations)) {
                                        $domain->update([
                                            'status' => $domainModel->present()
                                                                    ->getStatus('approved'),
                                        ]);

                                        foreach ($domain->project->shopifyIntegrations as $shopifyIntegration) {

                                            try {
                                                $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
                                                $shopify->setThemeByRole('main');
                                            } catch (Exception $e) {
                                                report($e);

                                                return response()->json([
                                                                            'message' => 'Ocorreu um erro, irregularidade na loja shopify',
                                                                        ], 400);
                                            }

                                            if (empty($shopifyIntegration->layout_theme_html)) {
                                                $html = $shopify->getTemplateHtml($shopify::templateKeyName);
                                                if (!empty($html) && $shopify->checkCartTemplate($html)) {

                                                    return response()->json(['message' => 'Domínio validado com sucesso'], 200);
                                                } else {

                                                    try {

                                                        $shopify->setThemeByRole('main');
                                                        $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');

                                                        if ($htmlCart) {
                                                            //template normal

                                                            $shopifyIntegration->update([
                                                                                            'theme_type' => $shopifyIntegration->present()
                                                                                                                               ->getThemeType('basic_theme'),
                                                                                            'theme_name' => $shopify->getThemeName(),
                                                                                            'theme_file' => 'sections/cart-template.liquid',
                                                                                            'theme_html' => $htmlCart,
                                                                                        ]);

                                                            $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, $domain->name);
                                                        } else {

                                                            //template ajax
                                                            /* $shopifyIntegration->update([
                                                                                             'theme_type' => $shopifyIntegration->present()
                                                                                                                                ->getThemeType('ajax_theme'),
                                                                                             'theme_name' => $shopify->getThemeName(),
                                                                                             'theme_file' => 'snippets/ajax-cart-template.liquid',
                                                                                             'theme_html' => $htmlCart,
                                                                                         ]);

                                                             $shopify->updateTemplateHtml('snippets/ajax-cart-template.liquid', $htmlCart, $domain->name, true);*/
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
                                                                            'status' => $domainModel->present()
                                                                                                    ->getStatus('pending'),
                                                                        ]);

                                                        return response()->json(['message' => 'Domínio validado com sucesso, mas a integração com o shopify não foi encontrada'], 400);
                                                    }

                                                    return response()->json(['message' => 'Domínio validado com sucesso'], 200);
                                                }
                                            } else {

                                            }
                                        }
                                    } else {
                                        // integração nao encontrada
                                        $domain->update([
                                                            'status' => $domainModel->present()->getStatus('pending'),
                                                        ]);

                                        return response()->json([
                                                                    'message' => 'Não foi possivel revalidar o domínio, integração do shopify não encontrada',
                                                                ], 400);
                                    }
                                } catch (Exception $e) {

                                }
                            } else {
                                // não e integracao shopify, validar dominio
                                $domain->update([
                                                    'status' => $domainModel->present()->getStatus('approved'),
                                                ]);

                                return response()->json([
                                                            'message' => 'Dominio revalidado com sucesso!',
                                                        ], 200);
                            }
                        } else {
                            $domain->update([
                                                'status' => $domainModel->present()->getStatus('pending'),
                                            ]);

                            return response()->json([
                                                        'message' => 'A verificação falhou, atualização de nameservers pendentes',
                                                    ], 400);
                        }
                    } else {
                        return response()->json([
                                                    'message' => 'Sem permissão para validar domínio',
                                                ], 403);
                    }
                } else {
                    //dominio nao existe
                    return response()->json(['message' => 'Domínio não encontrado'], 404);
                }
            } else {
                // hash invalida
                return response()->json([
                                            'message' => 'Não foi possivel revalidar o dominio',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('DomainsAPiController recheckOnly - erro ao revalidar o dominio');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um problema ao revalidar o domínio',
                                    ], 400);
        }
    }

    /**
     * @param $project
     * @param $domain
     * @return JsonResponse
     */
    public function show($project, $domain)
    {
        try {

            if (!empty($domain)) {
                $domainModel       = new Domain();
                $cloudFlareService = new CloudFlareService();

                $domain = $domainModel->with(['project'])->where('id', current(Hashids::decode($domain)))->first();

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

                    return response()->json([
                                                'message' => 'Dados do dominio',
                                                'data'    => ['id_code' => Hashids::encode($domain->id), 'zones' => $newNameServers, 'domainHost' => $domainHost, 'status' => $domain->status],
                                            ], 200);
                } else {
                    return response()->json(['message' => 'Sem permissão para visualizar o domínio'], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro, dominio nao encontrado',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados para validar dominio (DomainsApiController - getDomainData)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }
}
