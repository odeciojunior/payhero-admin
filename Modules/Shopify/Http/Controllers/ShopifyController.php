<?php

namespace Modules\Shopify\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Events\ShopifyIntegrationEvent;

/**
 * Class ShopifyController
 * @package Modules\Shopify\Http\Controllers
 */
class ShopifyController extends Controller
{
    /**
     * @return Factory|RedirectResponse|View
     */
    public function index()
    {
        try {

            $companyModel            = new Company();
            $projectModel            = new Project();
            $shopifyIntegrationModel = new ShopifyIntegration();

            $companies = $companyModel->where('user_id', auth()->user()->id)->get()->toArray();

            $shopifyIntegrations = $shopifyIntegrationModel->where('user_id', auth()->user()->id)->get();

            $projects = [];

            foreach ($shopifyIntegrations as $shopifyIntegration) {

                $project = $projectModel->find($shopifyIntegration->project_id);

                if (!empty($project)) {
                    $projects[] = $project;
                }
            }

            return view('shopify::index', [
                'companies' => $companies,
                'projects'  => $projects,
            ]);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar aplicativo integração shopify (ShopifyController - index)');
            report($e);

            return redirect()->back();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $dados = $request->all();

            $projectModel            = new Project();
            $userProjectModel        = new UserProject();
            $shopifyIntegrationModel = new ShopifyIntegration();
            $shippingModel           = new Shipping();

            $shopifyIntegration = $shopifyIntegrationModel
                ->where('token', $dados['token'])
                ->first();

            if ($shopifyIntegration) {
                if ($shopifyIntegration->status == 1) {
                    return response()->json(['message' => 'Integração em andamento'], 400);
                }

                return response()->json(['message' => 'Projeto já integrado'], 400);
            }

            try {
                //tratamento parcial do dominio
                $dados['url_store'] = str_replace("http://", "", $dados['url_store']);
                $dados['url_store'] = str_replace("https://", "", $dados['url_store']);
                $dados['url_store'] = 'http://' . $dados['url_store'];
                $dados['url_store'] = parse_url($dados['url_store'], PHP_URL_HOST);

                $urlStore = str_replace('.myshopify.com', '', $dados['url_store']);

                $shopifyService = new ShopifyService($urlStore . '.myshopify.com', $dados['token']);

                if (empty($shopifyService->getClient())) {
                    return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
                }
            } catch (Exception $e) {
                report($e);

                return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
            }

            $shopifyName = $shopifyService->getShopName();
            $project     = $projectModel->create([
                                                     'name'                       => $shopifyName,
                                                     'status'                     => $projectModel->getEnum('status', 'approved'),
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
            $shippingModel->create([
                                       'project_id'   => $project->id,
                                       'name'         => 'Frete gratis',
                                       'information'  => 'de 15 até 30 dias',
                                       'value'        => '0,00',
                                       'type'         => 'static',
                                       'status'       => '1',
                                       'pre_selected' => '1',
                                   ]);

            $shopifyIntegration = $shopifyIntegrationModel->create([
                                                                       'token'         => $dados['token'],
                                                                       'shared_secret' => '',
                                                                       'url_store'     => $urlStore . '.myshopify.com',
                                                                       'user'          => auth()->user()->id,
                                                                       'project_id'    => $project->id,
                                                                       'status'        => 1,
                                                                   ]);

            $userProjectModel->create([
                                          'user_id'              => auth()->user()->id,
                                          'project_id'           => $project->id,
                                          'company_id'           => $dados['company'],
                                          'type'                 => 'producer',
                                          'shipment_responsible' => true,
                                          'permissao_acesso'     => true,
                                          'permissao_editar'     => true,
                                          'status'               => 'active',
                                      ]);

            event(new ShopifyIntegrationEvent($shopifyIntegration, auth()->user()->id));

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
                    ->with(['domains', 'shopifyIntegrations', 'plans', 'plans.productsPlans', 'plans.productsPlans.product', 'pixels', 'discountCoupons', 'zenviaSms', 'shippings'])
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
                                                            'status' => $shopifyIntegration->getEnum('status', 'disabled'),
                                                        ]);
                        }

                        return response()->json(['message' => 'Integração com o shopify desfeita'], 200);
                    } catch (Exception $e) {
                        //throwl
                        return response()->json(['message' => 'Problema ao desfazer integração, tente novamente mais tarde'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Este projeto não tem integração com o shopify'], 400);
                }
            } else {
                //problema no id
                return response()->json(['message' => 'Projeto não encontrado'], 400);
            }
        } catch (Exception $e) {
            Log::warning('ShopifyController - undoIntegration - Erro ao desfazer integracao');
            report($e);

            return response()->json(['message' => 'Problema ao desfazer integração, tente novamente mais tarde'], 400);
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
                               'zenviaSms',
                               'shippings',
                           ])
                    ->find($projectId);

                //puxa todos os produtos
                foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                    $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
                    $shopify->importShopifyStore($projectId, auth()->user()->id);
                }

                //procura por um dominio aprovado
                $domain = $project->domains->where('status', 3)->first();

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
                                                                    'theme_type' => $shopifyIntegrationModel->getEnum('theme_type', 'basic_theme'),
                                                                    'theme_name' => $shopify->getThemeName(),
                                                                    'theme_file' => 'sections/cart-template.liquid',
                                                                    'theme_html' => $htmlCart,
                                                                ]);

                                    $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, $domain->name);
                                } else {
                                    //template ajax
                                    $shopifyIntegration->update([
                                                                    'theme_type' => $shopifyIntegrationModel->getEnum('theme_type', 'ajax_theme'),
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
                                                                'status' => $shopifyIntegration->getEnum('status', 'approved'),
                                                            ]);
                            }

                            return response()->json(['message' => 'Integração com o shopify refeita'], 200);
                        } catch (Exception $e) {
                            //throwl
                            return response()->json(['message' => 'Problema ao refazer integração, tente novamente mais tarde'], 400);
                        }
                    } else {
                        return response()->json(['message' => 'Este projeto não tem integração com o shopify'], 400);
                    }
                } else {
                    //nenhum dominio ativado
                    return response()->json(['message' => 'Produtos do shopify importados, adicione um domínio para finalizar a sua integração'], 200);
                }
            } else {
                //problema no id
                return response()->json(['message' => 'Projeto não encontrado'], 400);
            }
        } catch (Exception $e) {
            Log::warning('ShopifyController - reIntegration - Erro ao refazer integracao');
            report($e);

            return response()->json(['message' => 'Problema ao refazer integração, tente novamente mais tarde'], 400);
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

                event(new ShopifyIntegrationEvent($shopifyIntegration, auth()->user()->id));

                return response()->json(['message' => 'Os Produtos do shopify estão sendo sincronizados.'], 200);
            } else {
                return response()->json(['message' => 'Problema ao sincronizar produtos, tente novamente mais tarde'], 400);
            }
        } catch (Exception $e) {
            Log::critical('Erro ao realizar sincronização produtos com o shopify| ShopifyController@synchronizeProducts');
            report($e);

            return response()->json(['message' => 'Problema ao sincronizar produtos do shopify, tente novamente mais tarde'], 400);
        }
    }

    public function synchronizeTemplates(Request $request)
    {
        try {
            $requestData = $request->all();

            $projectModel            = new Project();
            $shopifyIntegrationModel = new ShopifyIntegration();

            $projectId = current(Hashids::decode($requestData['project_id']));

            if (!empty($projectId)) {
                $project = $projectModel->with([
                                                   'domains',
                                                   'shopifyIntegrations',
                                                   'plans',
                                                   'plans.productsPlans',
                                                   'plans.productsPlans.product',
                                                   'pixels', 'discountCoupons',
                                                   'zenviaSms',
                                                   'shippings',
                                               ])
                                        ->find($projectId);

                // procura dominio aprovado
                $domain = $project->domains->where('status', 3)->first();

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
                                                                    'theme_type' => $shopifyIntegrationModel->getEnum('theme_type', 'basic_theme'),
                                                                    'theme_name' => $shopify->getThemeName(),
                                                                    'theme_file' => 'sections/cart-template.liquid',
                                                                    'theme_html' => $htmlCart,
                                                                ]);

                                    $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, $domain->name);
                                } else {
                                    //template ajax
                                    $shopifyIntegration->update([
                                                                    'theme_type' => $shopifyIntegrationModel->getEnum('theme_type', 'ajax_theme'),
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
                                                                'status' => $shopifyIntegration->getEnum('status', 'approved'),
                                                            ]);
                            }

                            return response()->json(['message' => 'Sincronização do template com o shopify concluida com sucesso!'], 200);
                        } catch (Exception $e) {
                            //throwl
                            return response()->json(['message' => 'Problema ao refazer integração, tente novamente mais tarde'], 400);
                        }
                    } else {
                        return response()->json(['message' => 'Este projeto não tem integração com o shopify'], 400);
                    }
                } else {
                    return response()->json(['message' => 'Você não tem nenhum domínio configurado'], 400);
                }
            } else {
                return response()->json(['message' => 'Projeto não encontrado'], 400);
            }
        } catch (Exception $e) {
            Log::critical('Erro ao realizar sincronização produtos com o shopify| ShopifyController@synchronizeProducts');
            report($e);

            return response()->json(['message' => 'Problema ao sincronizar template do shopify, tente novamente mais tarde'], 400);
        }
    }
}

