<?php

namespace Modules\Shopify\Http\Controllers;

use Exception;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Domain;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Shipping;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Events\ShopifyIntegrationEvent;
use Modules\Shopify\Transformers\ShopifyResource;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ShopifyApiController
 * @package Modules\Shopify\Http\Controllers
 */
class ShopifyApiController extends Controller
{
    /**
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $projectModel            = new Project();
            $shopifyIntegrationModel = new ShopifyIntegration();

            $shopifyIntegrations = $shopifyIntegrationModel->where('user_id', auth()->user()->account_owner_id)->get();

            $projects = [];

            foreach ($shopifyIntegrations as $shopifyIntegration) {

                $project = $projectModel->where('id', $shopifyIntegration->project_id)
                                        ->where('status', $projectModel->present()->getStatus('active'))->first();

                if (!empty($project)) {
                    $projects[] = $project;
                }
            }

            return ShopifyResource::collection(collect($projects));
        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $projectModel            = new Project();
            $userProjectModel        = new UserProject();
            $shopifyIntegrationModel = new ShopifyIntegration();
            $shippingModel           = new Shipping();

            $dataRequest = $request->all();

            $shopifyIntegration = $shopifyIntegrationModel
                ->where('token', $dataRequest['token'])
                ->first();

            if ($shopifyIntegration) {
                if ($shopifyIntegration->status == 1) {
                    return response()->json(['message' => 'Integração em andamento'], 400);
                }

                return response()->json(['message' => 'Projeto já integrado'], 400);
            }

            try {
                //tratamento parcial do dominio
                $dataRequest['url_store'] = str_replace("http://", "", $dataRequest['url_store']);
                $dataRequest['url_store'] = str_replace("https://", "", $dataRequest['url_store']);
                $dataRequest['url_store'] = 'http://' . $dataRequest['url_store'];
                $dataRequest['url_store'] = parse_url($dataRequest['url_store'], PHP_URL_HOST);

                $urlStore = str_replace('.myshopify.com', '', $dataRequest['url_store']);

                $shopifyService = new ShopifyService($urlStore . '.myshopify.com', $dataRequest['token']);

                if (empty($shopifyService->getClient())) {
                    return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
                }
            } catch (Exception $e) {
                report($e);

                return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
            }

            if(!$shopifyService->verifyPermissions()){
                return response()->json(['message' => 'As permissões do token criado são insuficientes!'], 400);
            }

            $shopifyName = $shopifyService->getShopName();
            $project     = $projectModel->create([
                                                     'name'                       => $shopifyName,
                                                     'status'                     => $projectModel->present()
                                                                                                  ->getStatus('active'),
                                                     'visibility'                 => 'private',
                                                     'percentage_affiliates'      => '0',
                                                     'description'                => $shopifyName,
                                                     'invoice_description'        => $shopifyName,
                                                     'url_page'                   => 'https://' . $shopifyService->getShopDomain(),
                                                     'automatic_affiliation'      => false,
                                                     'shopify_id'                 => $shopifyService->getShopId(),
                                                     'boleto'                     => '1',
                                                     'installments_amount'        => '12',
                                                     'installments_interest_free' => '1',
                                                 ]);
            if (!empty($project)) {
                $shippingModel->create([
                                           'project_id'   => $project->id,
                                           'name'         => 'Frete gratis',
                                           'information'  => 'de 15 até 30 dias',
                                           'value'        => '0,00',
                                           'type'         => 'static',
                                           'status'       => '1',
                                           'pre_selected' => '1',
                                       ]);
                if (!empty($shippingModel)) {
                    $shopifyIntegration = $shopifyIntegrationModel->create([
                                                                               'token'         => $dataRequest['token'],
                                                                               'shared_secret' => '',
                                                                               'url_store'     => $urlStore . '.myshopify.com',
                                                                               'user_id'       => auth()->user()->id,
                                                                               'project_id'    => $project->id,
                                                                               'status'        => 1,
                                                                           ]);

                    if (!empty($shopifyIntegration)) {
                        $companyId = current(Hashids::decode($dataRequest['company']));

                        $userProjectModel->create([
                                                      'user_id'              => auth()->user()->id,
                                                      'project_id'           => $project->id,
                                                      'company_id'           => $companyId,
                                                      'type'                 => 'producer',
                                                      'shipment_responsible' => true,
                                                      'permissao_acesso'     => true,
                                                      'permissao_editar'     => true,
                                                      'status'               => 'active',
                                                  ]);
                        if (!empty($userProjectModel)) {

                            event(new ShopifyIntegrationEvent($shopifyIntegration, auth()->user()->account_owner_id));

                        } else {
                            $shippingModel->delete();
                            $shopifyIntegration->delete();
                            $project->delete();

                            return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
                        }
                    } else {
                        $shippingModel->delete();
                        $project->delete();

                        return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
                    }
                } else {
                    $project->delete();

                    return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
                }
            } else {
                return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
            }

            return response()->json(['message' => 'Integração em andamento. Assim que tudo estiver pronto você será avisado(a)!'], 200);
        } catch (Exception $e) {
            Log::critical('Erro ao realizar integração com loja do shopify | ShopifyController@store');
            report($e);

            return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function undoIntegration(Request $request)
    {
        try {
            $requestData = $request->all();

            $projectId = current(Hashids::decode($requestData['project_id']));

            $projectModel = new Project();

            if ($projectId) {
                //id decriptado
                $project = $projectModel
                    ->with(['domains', 'shopifyIntegrations', 'plans', 'plans.productsPlans', 'plans.productsPlans.product', 'pixels', 'discountCoupons', 'shippings'])
                    ->find($projectId);

                if (!empty($project->shopify_id)) {
                    //se for shopify, voltar as integraçoes ao html padrao
                    try {

                        foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                            $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                            $shopify->setThemeByRole('main');
                            if (!empty($shopifyIntegration->theme_html)) {
                                $shopify->setTemplateHtml($shopifyIntegration->theme_file, $shopifyIntegration->theme_html);
                            }
                            if (!empty($shopifyIntegration->layout_theme_html)) {
                                $shopify->setTemplateHtml('layout/theme.liquid', $shopifyIntegration->layout_theme_html);
                            }

                            //remove todos os webhooks
                            $shopify->deleteShopWebhook();

                            $shopifyIntegration->update([
                                                            'status' => $shopifyIntegration->present()
                                                                                           ->getStatus('disabled'),
                                                        ]);
                        }

                        return response()->json(['message' => 'Integração com o shopify desfeita'], Response::HTTP_OK);
                    } catch (Exception $e) {
                        //throwl
                        return response()->json(['message' => 'Problema ao desfazer integração, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    return response()->json(['message' => 'Este projeto não tem integração com o shopify'], Response::HTTP_BAD_REQUEST);
                }
            } else {
                //problema no id
                return response()->json(['message' => 'Projeto não encontrado'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::warning('ShopifyController - undoIntegration - Erro ao desfazer integracao');
            report($e);

            return response()->json(['message' => 'Problema ao desfazer integração, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function reIntegration(Request $request)
    {
        try {
            $requestData = $request->all();

            $projectId               = current(Hashids::decode($requestData['project_id']));
            $projectModel            = new Project();
            $shopifyIntegrationModel = new ShopifyIntegration();
            $domainModel             = new Domain();

            if ($projectId) {
                //id decriptado
                $project = $projectModel
                    ->with([
                               'domains',
                               'shopifyIntegrations',
                               'plans',
                               'plans.productsPlans',
                               'plans.productsPlans.product',
                               'pixels', 'discountCoupons',
                               'shippings',
                           ])
                    ->find($projectId);

                //puxa todos os produtos
                foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                    $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
                    $shopify->importShopifyStore($projectId, auth()->user()->account_owner_id);
                }

                //procura por um dominio aprovado
                $domain = $project->domains->where('status', $domainModel->present()->getStatus('approved'))->first();

                if (!empty($domain)) {
                    //primeiro dominio valido
                    if (!empty($project->shopify_id)) {
                        //se for shopify, voltar as integraçoes ao html padrao
                        try {

                            foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                                $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                                $shopify->setThemeByRole('main');
                                $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');

                                if ($htmlCart) {
                                    //template normal
                                    $shopifyIntegration->update([
                                                                    'theme_type' => $shopifyIntegrationModel->present()
                                                                                                            ->getThemeType('basic_theme'),
                                                                    'theme_name' => $shopify->getThemeName(),
                                                                    'theme_file' => 'sections/cart-template.liquid',
                                                                    'theme_html' => $htmlCart,
                                                                ]);

                                    $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, $domain->name);
                                } else {
                                    //template ajax
                                    $htmlCart = $shopify->getTemplateHtml('snippets/ajax-cart-template.liquid');

                                    $shopifyIntegration->update([
                                                                    'theme_type' => $shopifyIntegrationModel->present()
                                                                                                            ->getThemeType('ajax_theme'),
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

                                $shopifyIntegration->update([
                                                                'status' => $shopifyIntegration->present()
                                                                                               ->getStatus('approved'),
                                                            ]);
                            }

                            return response()->json(['message' => 'Integração com o shopify refeita'], Response::HTTP_OK);
                        } catch (Exception $e) {
                            //throwl
                            return response()->json(['message' => 'Problema ao refazer integração, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return response()->json(['message' => 'Este projeto não tem integração com o shopify'], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    //nenhum dominio ativado
                    return response()->json(['message' => 'Produtos do shopify importados, adicione um domínio para finalizar a sua integração'], Response::HTTP_OK);
                }
            } else {
                //problema no id
                return response()->json(['message' => 'Projeto não encontrado'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::warning('ShopifyController - reIntegration - Erro ao refazer integracao');
            report($e);

            return response()->json(['message' => 'Problema ao refazer integração, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function synchronizeProducts(Request $request)
    {
        try {
            $requestData = $request->all();
            $projectId   = current(Hashids::decode($requestData['project_id']));

            $shopifyModel = new ShopifyIntegration();
            if (!empty($projectId)) {

                $shopifyIntegration = $shopifyModel->where('project_id', $projectId)->first();
                if (!empty($shopifyIntegration)) {
                    event(new ShopifyIntegrationEvent($shopifyIntegration, auth()->user()->account_owner_id));

                    return response()->json(['message' => 'Os Produtos do shopify estão sendo sincronizados.'], Response::HTTP_OK);
                } else {
                    return response()->json(['message' => 'Problema ao sincronizar produtos, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
                }
            } else {
                return response()->json(['message' => 'Problema ao sincronizar produtos, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::critical('Erro ao realizar sincronização produtos com o shopify| ShopifyController@synchronizeProducts');
            report($e);

            return response()->json(['message' => 'Problema ao sincronizar produtos do shopify, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function synchronizeTemplates(Request $request)
    {
        try {
            $requestData = $request->all();

            $projectModel            = new Project();
            $shopifyIntegrationModel = new ShopifyIntegration();
            $domainModel             = new Domain();

            $projectId = current(Hashids::decode($requestData['project_id']));

            if (!empty($projectId)) {
                $project = $projectModel->with([
                                                   'domains',
                                                   'shopifyIntegrations',
                                                   'plans',
                                                   'plans.productsPlans',
                                                   'plans.productsPlans.product',
                                                   'pixels', 'discountCoupons',
                                                   'shippings',
                                               ])
                                        ->find($projectId);

                // procura dominio aprovado
                $domain = $project->domains->where('status', $domainModel->present()->getStatus('approved'))->first();

                if (!empty($domain)) {
                    if (!empty($project->shopify_id)) {
                        try {

                            foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                                $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                                $shopify->setThemeByRole('main');
                                $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');

                                if ($htmlCart) {
                                    //template normal
                                    $shopifyIntegration->update([
                                                                    'theme_type' => $shopifyIntegrationModel->present()
                                                                                                            ->getThemeType('basic_theme'),
                                                                    'theme_name' => $shopify->getThemeName(),
                                                                    'theme_file' => 'sections/cart-template.liquid',
                                                                    'theme_html' => $htmlCart,
                                                                ]);

                                    $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, $domain->name);
                                } else {

                                    $htmlCart = $shopify->getTemplateHtml('snippets/ajax-cart-template.liquid');

                                    //template ajax
                                    $shopifyIntegration->update([
                                                                    'theme_type' => $shopifyIntegrationModel->present()
                                                                                                            ->getThemeType('ajax_theme'),
                                                                    'theme_name' => $shopify->getThemeName(),
                                                                    'theme_file' => 'snippets/ajax-cart-template.liquid',
                                                                    'theme_html' => $htmlCart,
                                                                ]);

                                    //$shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, $domain->name);
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

                                $shopifyIntegration->update([
                                                                'status' => $shopifyIntegration->present()
                                                                                               ->getStatus('approved'),
                                                            ]);
                            }

                            return response()->json(['message' => 'Sincronização do template com o shopify concluida com sucesso!'], Response::HTTP_OK);
                        } catch (Exception $e) {
                            //throwl
                            return response()->json(['message' => 'Problema ao refazer integração, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return response()->json(['message' => 'Este projeto não tem integração com o shopify'], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    return response()->json(['message' => 'Você não tem nenhum domínio configurado'], Response::HTTP_BAD_REQUEST);
                }
            } else {
                return response()->json(['message' => 'Projeto não encontrado'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::critical('Erro ao realizar sincronização produtos com o shopify| ShopifyController@synchronizeProducts');
            report($e);

            return response()->json(['message' => 'Problema ao sincronizar template do shopify, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getCompanies()
    {
        try {

            $companyModel = new Company();
            $companies    = $companyModel->where('user_id', auth()->user()->account_owner_id)->get();

            return CompaniesSelectResource::collection($companies);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar abrir modal de integração shopify');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }
}
