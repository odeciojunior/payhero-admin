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

            $shopifyIntegrations = $shopifyIntegrationModel->where('user_id', auth()->user()->id)->get();

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
            $dataRequest = $request->all();

            $shopifyIntegrationModel = new ShopifyIntegration();

            $dataRequest['url_store'] = str_replace("http://", "", $dataRequest['url_store']);
            $dataRequest['url_store'] = str_replace("https://", "", $dataRequest['url_store']);
            $dataRequest['url_store'] = 'http://' . $dataRequest['url_store'];
            $dataRequest['url_store'] = parse_url($dataRequest['url_store'], PHP_URL_HOST);

            $urlStore = str_replace('.myshopify.com', '', $dataRequest['url_store']);

            $shopifyIntegration = $shopifyIntegrationModel->where('url_store', $urlStore . '.myshopify.com')->first();

            if (!empty($shopifyIntegration)) {
                return response()->json(['message' => 'Erro! Integração já existe!'], Response::HTTP_BAD_REQUEST);
            }

            //TODO: ajustar
            $config = new \SocialiteProviders\Manager\Config(
                env('SHOPIFY_KEY'),
                env('SHOPIFY_SECRET'),
                env('APP_ENV') == 'production' ? 'https://app.cloudfox.net/apps/shopify/login/callback' :
                    env('APP_URL') ?? 'http://3b52cfbd.ngrok.io/apps/shopify/login/callback',
                ['subdomain' => $urlStore]
            );

            $redirectAuthUrl = Socialite::with('shopify')
                                        ->setConfig($config)
                                        ->scopes([
                                                     'read_customers',
                                                     'write_customers',
                                                     'read_inventory',
                                                     'write_inventory',
                                                     'read_order_edits',
                                                     'write_order_edits',
                                                     'read_orders',
                                                     'write_orders',
                                                     'write_product_listings',
                                                     'read_product_listings',
                                                     'write_products',
                                                     'read_products',
                                                     'write_themes',
                                                     'read_themes',
                                                     'write_fulfillments',
                                                     'read_fulfillments',
                                                     'read_assigned_fulfillment_orders',
                                                     'write_assigned_fulfillment_orders',
                                                 ])
                                        ->stateless()
                                        ->with(['state' => $dataRequest['company']])
                                        ->redirect()
                                        ->getTargetUrl();

            return response()->json([
                                        'message' => 'URL gerada com sucesso',
                                        'data'    => [
                                            'auth_shopify_url' => $redirectAuthUrl,
                                        ],
                                    ], Response::HTTP_OK);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function callbackShopifyIntegration(Request $request)
    {

        $integrationToken = Socialite::driver('shopify')->stateless()->user()->token;

        $projectModel            = new Project();
        $userProjectModel        = new UserProject();
        $shopifyIntegrationModel = new ShopifyIntegration();
        $shippingModel           = new Shipping();
        $shopifyService          = new ShopifyService($request->shop, $integrationToken);

        try {
            $shopifyName = $shopifyService->getShopName();

            $project = $projectModel->create([
                                                 'name'                       => $shopifyName,
                                                 'status'                     => $projectModel->present()
                                                                                              ->getStatus('active'),
                                                 'description'                => $shopifyName,
                                                 'invoice_description'        => $shopifyName,
                                                 'url_page'                   => 'https://' . $shopifyService->getShopDomain(),
                                                 'shopify_id'                 => $shopifyService->getShopId(),
                                                 'visibility'                 => 'private',
                                                 'percentage_affiliates'      => '0',
                                                 'automatic_affiliation'      => false,
                                                 'boleto'                     => '1',
                                                 'installments_amount'        => '12',
                                                 'installments_interest_free' => '1',
                                             ]);
            if (!empty($project)) {
                $shipping = $shippingModel->create([
                                                       'project_id'   => $project->id,
                                                       'name'         => 'Frete gratis',
                                                       'information'  => 'de 15 até 30 dias',
                                                       'value'        => '0,00',
                                                       'type'         => 'static',
                                                       'status'       => '1',
                                                       'pre_selected' => '1',
                                                   ]);
                if (!empty($shipping)) {
                    $shopifyIntegration = $shopifyIntegrationModel->create([
                                                                               'shared_secret' => '',
                                                                               'url_store'     => $request->shop,
                                                                               'user_id'       => auth()->user()->id,
                                                                               'project_id'    => $project->id,
                                                                               'status'        => 1,
                                                                               'token'         => $integrationToken,
                                                                           ]);
                    if (!empty($shopifyIntegration)) {

                        $companyId = current(Hashids::decode(request()->input('state')));

                        $userProject = $userProjectModel->create([
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
                            event(new ShopifyIntegrationEvent($shopifyIntegration, auth()->user()->id));
                        } else {
                            Log::warning('callback shopfiy - erro 1');
                            $shipping->delete();
                            $shopifyIntegration->delete();
                            $project->delete();

                            return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
                        }

                        return response()->redirectTo('/apps/shopify');
                    } else {
                        Log::warning('callback shopfiy - erro 2');
                        $shipping->delete();
                        $project->delete();

                        return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    Log::warning('callback shopfiy - erro 3');
                    $project->delete();

                    return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
                }
            } else {
                Log::warning('callback shopfiy - erro 4');

                return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
            }

            return response()->json(['message' => 'Integração em andamento. Assim que tudo estiver pronto você será avisado(a)!'], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::critical('Erro ao realizar integração com loja do shopify | ShopifyController@store');
            report($e);
            Log::warning('callback shopfiy - erro 5');

            return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], Response::HTTP_BAD_REQUEST);
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
                    $shopify->importShopifyStore($projectId, auth()->user()->id);
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
        //            return response()->json(['message' => 'Funfou!'], Response::HTTP_OK);
        try {
            $requestData = $request->all();
            $projectId   = current(Hashids::decode($requestData['project_id']));
            /** @var ShopifyIntegration $shopifyModel */
            $shopifyModel = new ShopifyIntegration();
            if (!empty($projectId)) {
                /** @var ShopifyIntegration $shopifyIntegration */
                $shopifyIntegration = $shopifyModel->where('project_id', $projectId)->first();
                if (!empty($shopifyIntegration)) {
                    event(new ShopifyIntegrationEvent($shopifyIntegration, auth()->user()->id));

                    //                    $this->teste($shopifyIntegration, auth()->user()->id);

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
            $companies    = $companyModel->where('user_id', auth()->user()->id)->get();

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
