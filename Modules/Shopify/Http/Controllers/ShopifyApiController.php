<?php

namespace Modules\Shopify\Http\Controllers;

use App\Jobs\ImportShopifyTrackingCodesJob;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Events\ShopifyIntegrationEvent;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\ShopifyErrors;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\UserService;
use Modules\Shopify\Transformers\ShopifyResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ShopifyApiController
 * @package Modules\Shopify\Http\Controllers
 */
class ShopifyApiController extends Controller
{
    public function index()
    {
        try {
            $projectModel = new Project();
            $shopifyIntegrationModel = new ShopifyIntegration();

            activity()->on($shopifyIntegrationModel)->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log('Visualizou tela todos as integrações com o shopify');

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

    public function store(Request $request)
    {
        try {
            $companyService = new CompanyService();
            $userService = new UserService();

            $companyDocumentPending = $companyService->haveAnyDocumentPending();
            $userDocumentPending = $userService->haveAnyDocumentPending();
            if ($companyDocumentPending || $userDocumentPending) {
                return response()->json(['message' => 'Finalize seu cadastro para integrar com Shopify'], 400);
            }

            $projectModel = new Project();
            $userProjectModel = new UserProject();
            $shopifyIntegrationModel = new ShopifyIntegration();
            $shippingModel = new Shipping();

            $dataRequest = $request->all();

            if (empty($dataRequest['company'])) {
                return response()->json([
                    'message' => 'A empresa precisa estar aprovada transacionar para realizar a integração!'
                ], 400);
            }

            //tratamento parcial do dominio
            $dataRequest['url_store'] = str_replace("http://", "", $dataRequest['url_store']);
            $dataRequest['url_store'] = str_replace("https://", "", $dataRequest['url_store']);

            $shopifyIntegration = $shopifyIntegrationModel
                ->where('url_store', $dataRequest['url_store'] . '.myshopify.com')
                ->orWhere('token', $dataRequest['token'])->first();

            if ($shopifyIntegration) {
                if ($shopifyIntegration->status == 1) {
                    return response()->json(['message' => 'Integração em andamento'], 400);
                }

                return response()->json(['message' => 'Projeto já integrado'], 400);
            }

            try {
                $dataRequest['url_store'] = 'http://' . $dataRequest['url_store'];
                $dataRequest['url_store'] = parse_url($dataRequest['url_store'], PHP_URL_HOST);

                $urlStore = str_replace('.myshopify.com', '', $dataRequest['url_store']);

                $shopifyService = new ShopifyService($urlStore . '.myshopify.com', $dataRequest['token']);

                if (empty($shopifyService->getClient())) {
                    return response()->json([
                        'message' => 'Dados do shopify inválidos, revise os dados informados'
                    ], 400);
                }
            } catch (Exception $e) {
                if (method_exists($e, 'getCode')) {
                    if ($e->getCode() == 401) {
                        return response()->json(
                            ['message' => 'Dados do shopify inválidos, revise os dados informados'],
                            400
                        );
                    } elseif ($e->getCode() == 402) {
                        return response()->json(['message' => 'Pagamento pendente na sua loja do Shopify'], 400);
                    } elseif ($e->getCode() == 403) {
                        return response()->json(
                            ['message' => 'Verifique as permissões de seu aplicativo no Shopify'],
                            400
                        );
                    } elseif ($e->getCode() == 404) {
                        return response()->json(
                            ['message' => 'Url da loja não encontrada, revise os dados informados'],
                            400
                        );
                    } elseif ($e->getCode() == 423) {
                        return response()->json(
                            ['message' => 'Loja bloqueada, entre em contato com o suporte do Shopify'],
                            400
                        );
                    } elseif ($e->getCode() == 429) {
                        return response()->json(['message' => 'Limite de requisiçoes atingido, tente novamente'], 400);
                    }
                }
                if (strpos($e->getMessage(), 'Shop name should be') !== false) {
                    return response()->json(['message' => 'Url inválida, revise os dados informados'], 400);
                }
                report($e);

                return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
            }

            $tokenPermissions = $shopifyService->verifyPermissions();
            if ($tokenPermissions['status'] == 'error') {
                return response()->json(['message' => $tokenPermissions['message']], 400);
            }

            $shopifyName = $shopifyService->getShopName();
            $projectCreated = $projectModel->create([
                'name' => $shopifyName,
                'status' => $projectModel->present()->getStatus('active'),
                'visibility' => 'private',
                'percentage_affiliates' => '0',
                'description' => $shopifyName,
                'invoice_description' => $shopifyName,
                'url_page' => 'https://' . $shopifyService->getShopDomain(),
                'automatic_affiliation' => false,
                'shopify_id' => $shopifyService->getShopId(),
                'boleto' => '1',
                'installments_amount' => '12',
                'installments_interest_free' => '1',
                'checkout_type' => 2 // checkout de 1 passo
            ]);

            if (empty($projectCreated)) {
                return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
            }

            $shippingCreated = $shippingModel->create([
                'project_id' => $projectCreated->id,
                'name' => 'Frete gratis',
                'information' => 'de 15 até 30 dias',
                'value' => '0,00',
                'type' => 'static',
                'type_enum' => $shippingModel->present()->getTypeEnum('static'),
                'status' => '1',
                'pre_selected' => '1',
            ]);

            if (empty($shippingCreated)) {
                $projectCreated->delete();

                return response()->json([
                    'message' => 'Problema ao criar integração, tente novamente mais tarde'
                ], 400);
            }

            $shopifyIntegrationCreated = $shopifyIntegrationModel->create([
                'token' => $dataRequest['token'],
                'shared_secret' => '',
                'url_store' => $urlStore . '.myshopify.com',
                'user_id' => auth()->user()->id,
                'project_id' => $projectCreated->id,
                'status' => 1,
            ]);

            if (empty($shopifyIntegrationCreated)) {
                $shippingCreated->delete();
                $projectCreated->delete();

                return response()->json([
                    'message' => 'Problema ao criar integração, tente novamente mais tarde'
                ], 400);
            }

            $userProjectCreated = $userProjectModel->create([
                'user_id' => auth()->user()->account_owner_id,
                'project_id' => $projectCreated->id,
                'company_id' => current(Hashids::decode($dataRequest['company'])),
                'type' => 'producer',
                'type_enum' => $userProjectModel->present()->getTypeEnum('producer'),
                'shipment_responsible' => true,
                'access_permission' => true,
                'edit_permission' => true,
                'status' => 'active',
                'status_flag' => $userProjectModel->present()->getStatusFlag('active'),
            ]);

            if (empty($userProjectCreated)) {
                $shopifyIntegrationCreated->delete();
                $shippingCreated->delete();
                $projectCreated->delete();

                return response()->json([
                    'message' => 'Problema ao criar integração, tente novamente mais tarde'
                ], 400);
            }

            $projectNotificationService = new ProjectNotificationService();
            $projectService = new ProjectService();
            $projectNotificationService->createProjectNotificationDefault($projectCreated->id);
            $projectService->createUpsellConfig($projectCreated->id);

            event(new ShopifyIntegrationEvent($shopifyIntegrationCreated, auth()->user()->account_owner_id));

            return response()->json([
                'message' => 'Integração em andamento. Assim que tudo estiver pronto você será avisado(a)!'
            ], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
        }
    }

    public function undoIntegration(Request $request)
    {
        try {
            $requestData = $request->all();

            $projectId = current(Hashids::decode($requestData['project_id']));

            $projectModel = new Project();

            if (FoxUtils::isEmpty($projectId)) {
                return response()->json(['message' => 'Projeto não encontrado'], Response::HTTP_BAD_REQUEST);
            }

            $project = $projectModel->with(['shopifyIntegrations'])->find($projectId);

            if (empty($project->shopify_id)) {
                return response()->json([
                    'message' => 'Este projeto não tem integração com o shopify'
                ], 400);
            }

            activity()->on($projectModel)->tap(
                function (Activity $activity) use ($projectId) {
                    $activity->log_name = 'updated';
                    $activity->subject_id = current(Hashids::decode($projectId));
                }
            )->log('Integração com o shopify desfeita para o projeto ' . $project->name);

            try {
                foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                    $shopify = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                    $shopify->setThemeByRole('main');
                    if (!empty($shopifyIntegration->theme_html)) {
                        $shopify->setTemplateHtml(
                            $shopifyIntegration->theme_file,
                            $shopifyIntegration->theme_html
                        );
                    }
                    if (!empty($shopifyIntegration->layout_theme_html)) {
                        $shopify->setTemplateHtml(
                            'layout/theme.liquid',
                            $shopifyIntegration->layout_theme_html
                        );
                    }

                    //remove todos os webhooks
                    $shopify->deleteShopWebhook();

                    $shopifyIntegration->update([
                        'status' => $shopifyIntegration->present()->getStatus('disabled'),
                    ]);
                }

                return response()->json(['message' => 'Integração com o shopify desfeita'], Response::HTTP_OK);
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Problema ao desfazer integração, tente novamente mais tarde'
                ], 400);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Problema ao desfazer integração, tente novamente mais tarde'
            ], 400);
        }
    }

    public function reIntegration(Request $request)
    {
        try {
            $requestData = $request->all();

            $projectId = current(Hashids::decode($requestData['project_id']));
            $projectModel = new Project();
            $shopifyIntegrationModel = new ShopifyIntegration();
            $domainModel = new Domain();

            if ($projectId) {
                //id decriptado
                $project = $projectModel
                    ->with(
                        [
                            'domains',
                            'shopifyIntegrations',
                            'plans',
                            'plans.productsPlans',
                            'plans.productsPlans.product',
                            'pixels',
                            'discountCoupons',
                            'shippings',
                        ]
                    )
                    ->find($projectId);

                activity()->on($shopifyIntegrationModel)->tap(
                    function (Activity $activity) {
                        $activity->log_name = 'updated';
                    }
                )->log('Reintegração do shopify para o projeto ' . $project->name);

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
                                $shopify = new ShopifyService(
                                    $shopifyIntegration->url_store, $shopifyIntegration->token
                                );

                                $shopify->setSkipToCart($shopifyIntegration->skip_to_cart);

                                $shopify->setThemeByRole('main');
                                $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');

                                if ($htmlCart) {
                                    //template normal
                                    $shopifyIntegration->update(
                                        [
                                            'theme_type' => $shopifyIntegrationModel->present()
                                                ->getThemeType('basic_theme'),
                                            'theme_name' => $shopify->getThemeName(),
                                            'theme_file' => 'sections/cart-template.liquid',
                                            'theme_html' => $htmlCart,
                                        ]
                                    );

                                    $shopify->updateTemplateHtml(
                                        'sections/cart-template.liquid',
                                        $htmlCart,
                                        $domain->name
                                    );
                                } else {
                                    //template ajax
                                    $htmlCart = $shopify->getTemplateHtml('snippets/ajax-cart-template.liquid');

                                    $shopifyIntegration->update(
                                        [
                                            'theme_type' => $shopifyIntegrationModel->present()
                                                ->getThemeType('ajax_theme'),
                                            'theme_name' => $shopify->getThemeName(),
                                            'theme_file' => 'snippets/ajax-cart-template.liquid',
                                            'theme_html' => $htmlCart,
                                        ]
                                    );

                                    $shopify->updateTemplateHtml(
                                        'snippets/ajax-cart-template.liquid',
                                        $htmlCart,
                                        $domain->name,
                                        true
                                    );
                                }

                                //inserir o javascript para o trackeamento (src, utm)
                                $htmlBody = $shopify->getTemplateHtml('layout/theme.liquid');
                                if ($htmlBody) {
                                    //template do layout
                                    $shopifyIntegration->update(
                                        [
                                            'layout_theme_html' => $htmlBody,
                                        ]
                                    );

                                    $shopify->insertUtmTracking('layout/theme.liquid', $htmlBody);
                                }

                                $shopifyIntegration->update(
                                    [
                                        'status' => $shopifyIntegration->present()
                                            ->getStatus('approved'),
                                    ]
                                );
                            }

                            return response()->json(
                                ['message' => 'Integração com o shopify refeita'],
                                Response::HTTP_OK
                            );
                        } catch (Exception $e) {
                            //throwl
                            return response()->json(
                                ['message' => 'Problema ao refazer integração, tente novamente mais tarde'],
                                Response::HTTP_BAD_REQUEST
                            );
                        }
                    } else {
                        return response()->json(
                            ['message' => 'Este projeto não tem integração com o shopify'],
                            Response::HTTP_BAD_REQUEST
                        );
                    }
                } else {
                    //nenhum dominio ativado
                    return response()->json(
                        ['message' => 'Produtos do shopify importados, adicione um domínio para finalizar a sua integração'],
                        Response::HTTP_OK
                    );
                }
            } else {
                //problema no id
                return response()->json(['message' => 'Projeto não encontrado'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::warning('ShopifyController - reIntegration - Erro ao refazer integracao');
            report($e);

            return response()->json(
                ['message' => 'Problema ao refazer integração, tente novamente mais tarde'],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function synchronizeProducts(Request $request)
    {
        try {
            $requestData = $request->all();

            $projectId = current(Hashids::decode($requestData['project_id']));
            $shopifyModel = new ShopifyIntegration();
            $projectModel = new Project();
            $project = $projectModel->find($projectId);

            activity()->on($shopifyModel)->tap(
                function (Activity $activity) {
                    $activity->log_name = 'updated';
                }
            )->log('Sicronizou produtos do shopify para o projeto ' . $project->name);

            if (!empty($projectId)) {
                $shopifyIntegration = $shopifyModel->where('project_id', $projectId)->first();
                if (!empty($shopifyIntegration)) {
                    event(new ShopifyIntegrationEvent($shopifyIntegration, auth()->user()->account_owner_id));

                    return response()->json(
                        ['message' => 'Os Produtos do shopify estão sendo sincronizados.'],
                        Response::HTTP_OK
                    );
                } else {
                    return response()->json(
                        ['message' => 'Problema ao sincronizar produtos, tente novamente mais tarde'],
                        Response::HTTP_BAD_REQUEST
                    );
                }
            } else {
                return response()->json(
                    ['message' => 'Problema ao sincronizar produtos, tente novamente mais tarde'],
                    Response::HTTP_BAD_REQUEST
                );
            }
        } catch (Exception $e) {
            Log::critical(
                'Erro ao realizar sincronização produtos com o shopify| ShopifyController@synchronizeProducts'
            );
            report($e);

            return response()->json(
                ['message' => 'Problema ao sincronizar produtos do shopify, tente novamente mais tarde'],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function synchronizeTrackings(Request $request)
    {
        try {
            $requestData = $request->all();
            $projectModel = new Project();

            $project = $projectModel->find(current(Hashids::decode($requestData['project_id'])));

            ImportShopifyTrackingCodesJob::dispatch($project);

            return response()->json(
                [
                    'message' => 'Os códigos de rastreio sendo importados...',
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            if (method_exists($e, 'getCode') && in_array($e->getCode(), [401, 402, 403, 404, 406, 422, 423, 429])) {
                return response()->json(
                    ['message' => 'Problema ao sincronizar códigos de rastreio do shopify, tente novamente mais tarde'],
                    Response::HTTP_BAD_REQUEST
                );
            }
            report($e);

            return response()->json(
                ['message' => 'Problema ao sincronizar códigos de rastreio do shopify, tente novamente mais tarde'],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function synchronizeTemplates(Request $request)
    {
        try {
            $requestData = $request->all();

            $projectModel = new Project();
            $shopifyIntegrationModel = new ShopifyIntegration();
            $domainModel = new Domain();

            $projectId = current(Hashids::decode($requestData['project_id']));

            if (!empty($projectId)) {
                $project = $projectModel->with(
                    [
                        'domains',
                        'shopifyIntegrations',
                        'plans',
                        'plans.productsPlans',
                        'plans.productsPlans.product',
                        'pixels',
                        'discountCoupons',
                        'shippings',
                    ]
                )
                    ->find($projectId);

                activity()->on($shopifyIntegrationModel)->tap(
                    function (Activity $activity) {
                        $activity->log_name = 'updated';
                    }
                )->log('Sicronizou template do shopify para o projeto ' . $project->name);

                $domain = $project->domains->where('status', $domainModel->present()->getStatus('approved'))->first();

                if (!empty($domain)) {
                    if (!empty($project->shopify_id)) {
                        try {
                            foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                                $shopify = new ShopifyService(
                                    $shopifyIntegration->url_store, $shopifyIntegration->token
                                );

                                $shopify->setThemeByRole('main');
                                $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');

                                if ($htmlCart) {
                                    $shopifyIntegration->update(
                                        [
                                            'theme_type' => $shopifyIntegrationModel->present()
                                                ->getThemeType('basic_theme'),
                                            'theme_name' => $shopify->getThemeName(),
                                            'theme_file' => 'sections/cart-template.liquid',
                                            'theme_html' => $htmlCart,
                                        ]
                                    );

                                    $shopify->updateTemplateHtml(
                                        'sections/cart-template.liquid',
                                        $htmlCart,
                                        $domain->name
                                    );
                                } else {
                                    $htmlCart = $shopify->getTemplateHtml('snippets/ajax-cart-template.liquid');

                                    if (empty($htmlCart)) {
                                        return response()->json(
                                            ['message' => 'Problema ao refazer integração, template \'ajax-cart-template.liquid\' não encontrado'],
                                            Response::HTTP_BAD_REQUEST
                                        );
                                    }

                                    $shopifyIntegration->update(
                                        [
                                            'theme_type' => $shopifyIntegrationModel->present()
                                                ->getThemeType('ajax_theme'),
                                            'theme_name' => $shopify->getThemeName(),
                                            'theme_file' => 'snippets/ajax-cart-template.liquid',
                                            'theme_html' => $htmlCart,
                                        ]
                                    );

                                    $shopify->updateTemplateHtml(
                                        'snippets/ajax-cart-template.liquid',
                                        $htmlCart,
                                        $domain->name,
                                        true
                                    );
                                }

                                $htmlBody = $shopify->getTemplateHtml('layout/theme.liquid');
                                if ($htmlBody) {
                                    $shopifyIntegration->update(
                                        [
                                            'layout_theme_html' => $htmlBody,
                                        ]
                                    );

                                    $shopify->insertUtmTracking('layout/theme.liquid', $htmlBody);
                                }

                                $shopifyIntegration->update(
                                    [
                                        'status' => $shopifyIntegration->present()
                                            ->getStatus('approved'),
                                    ]
                                );
                            }

                            return response()->json(
                                ['message' => 'Sincronização do template com o shopify concluida com sucesso!'],
                                Response::HTTP_OK
                            );
                        } catch (Exception $e) {
                            $message = ShopifyErrors::FormatErrors($e->getMessage());

                            if (empty($message)) {
                                report($e);
                                $message = 'Problema ao refazer integração, tente novamente mais tarde';
                            }

                            return response()->json(['message' => $message], Response::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return response()->json(
                            ['message' => 'Este projeto não tem integração com o shopify'],
                            Response::HTTP_BAD_REQUEST
                        );
                    }
                } else {
                    return response()->json(
                        ['message' => 'Você não tem nenhum domínio configurado'],
                        Response::HTTP_BAD_REQUEST
                    );
                }
            } else {
                return response()->json(['message' => 'Projeto não encontrado'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(
                ['message' => 'Problema ao sincronizar template do shopify, tente novamente mais tarde'],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function getCompanies()
    {
        try {
            $companyModel = new Company();
            $companies = $companyModel->where('user_id', auth()->user()->account_owner_id)->get();

            return CompaniesSelectResource::collection($companies);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar abrir modal de integração shopify');
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
                ],
                400
            );
        }
    }

    public function updateToken(Request $request)
    {
        try {
            $data = $request->all();
            $shopifyIntegrationModel = new ShopifyIntegration();
            $projectModel = new Project();


            if (empty($data['token'])) {
                return response()->json(
                    [
                        'message' => 'Ocorreu um erro ao atualizar token, tente novamente mais tarde',
                    ],
                    400
                );
            }

            if (strlen($data['token']) < 10) {
                return response()->json(
                    ['message' => 'Token inválido, o token de acesso deve ter entre 10 e 100 caracteres'],
                    400
                );
            }


            $projectId = current(Hashids::decode($data['project_id']));
            $project = $projectModel->find($projectId);

            if (empty($projectId)) {
                return response()->json(
                    [
                        'message' => 'Ocorreu um erro ao atualizar token, tente novamente mais tarde',
                    ],
                    400
                );
            }

            activity()->on($shopifyIntegrationModel)->tap(
                function (Activity $activity) {
                    $activity->log_name = 'updated';
                }
            )->log('Atualizou token de integração do shopify para o projeto ' . $project->name);


            $integration = $shopifyIntegrationModel->where('project_id', $projectId)->first();

            $shopify = new ShopifyService($integration->url_store, $data['token']);

            $permissions = $shopify->verifyPermissions();

            if ($permissions['status'] == 'error') {
                return response()->json(
                    [
                        'message' => $permissions['message'],
                    ],
                    400
                );
            }

            $integrationUpdated = $integration->update(['token' => $data['token']]);

            if ($integrationUpdated) {
                return response()->json(['message' => 'Token atualizado com sucesso'], 200);
            }

            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao atualizar token, tente novamente mais tarde',
                ],
                400
            );
        } catch (Exception $e) {
            $message = ShopifyErrors::FormatErrors($e->getMessage());

            if (empty($message)) {
                report($e);
                $message = 'Ocorreu um erro ao atualizar token, tente novamente mais tarde';
            }

            return response()->json(
                [
                    'message' => $message,
                ],
                400
            );
        }
    }

    public function verifyPermission(Request $request)
    {
        $data = $request->all();
        $shopifyIntegrationModel = new ShopifyIntegration();
        $projectModel = new Project();

        if (!empty($data['project_id'])) {
            $projectId = current(Hashids::decode($data['project_id']));
            $project = $projectModel->find($projectId);

            activity()->on($shopifyIntegrationModel)->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log('Verificação de permissões do token de integração do shopify para o projeto: ' . $project->name);

            if ($projectId) {
                $integration = $shopifyIntegrationModel->where('project_id', $projectId)->first();

                try {
                    $shopify = new ShopifyService($integration->url_store, $integration->token);
                    if (empty($shopify->getClient())) {
                        return response()->json(['message' => 'Token inválido, revise o dado informado'], 400);
                    }
                } catch (Exception $e) {
                    if (method_exists($e, 'getCode')) {
                        if ($e->getCode() == 401) {
                            return response()->json(['message' => 'Token inválido'], 400);
                        } elseif ($e->getCode() == 402) {
                            return response()->json(['message' => 'Pagamento pendente na sua loja do Shopify'], 400);
                        } elseif ($e->getCode() == 403) {
                            return response()->json(['message' => 'Erro nas permissões de seu aplicativo'], 400);
                        } elseif ($e->getCode() == 404) {
                            return response()->json(['message' => 'Url da loja não encontrada'], 400);
                        } elseif ($e->getCode() == 423) {
                            return response()->json(['message' => 'Loja bloqueada no Shopify'], 400);
                        } elseif ($e->getCode() == 429) {
                            return response()->json(['message' => 'Limite de requisiçoes atingido'], 400);
                        }
                    }
                    report($e);

                    return response()->json(['message' => 'Token inválido'], 400);
                }

                $permissions = $shopify->verifyPermissions();

                if ($permissions['status'] == 'error') {
                    return response()->json(
                        [
                            'message' => $permissions['message'],
                        ],
                        400
                    );
                } else {
                    return response()->json(
                        [
                            'message' => 'Todas as permissões estão funcionando corretamente',
                        ],
                        200
                    );
                }
            } else {
                return response()->json(
                    [
                        'message' => 'Ocorreu um erro ao verificar permissões, tente novamente mais tarde',
                    ],
                    400
                );
            }
        } else {
            return response()->json(
                [
                    'message' => 'Ocorreu um erro ao verificar permissões, tente novamente mais tarde',
                ],
                400
            );
        }
    }

    public function setSkipToCart(Request $request)
    {
        $data = $request->all();
        $shopifyIntegrationModel = new ShopifyIntegration();
        $projectModel = new Project();

        if (!empty($data['project_id']) && isset($data['skip_to_cart'])) {
            $projectId = current(Hashids::decode($data['project_id']));
            $project = $projectModel->with(['domains'])->find($projectId);

            if ($projectId) {
                $integration = $shopifyIntegrationModel->where('project_id', $projectId)->first();

                try {
                    if (FoxUtils::isProduction()) {
                        $shopify = new ShopifyService($integration->url_store, $integration->token);

                        $shopify->setSkipToCart(boolval($data['skip_to_cart']));

                        $shopify->setThemeByRole('main');

                        $htmlCart = $shopify->getTemplateHtml('sections/cart-template.liquid');

                        $domain = $project->domains->first();
                        $domainName = $domain ? $domain->name : null;

                        $shopify->updateTemplateHtml('sections/cart-template.liquid', $htmlCart, $domainName);

                        $integration->skip_to_cart = boolval($data['skip_to_cart']);
                        $integration->save();

                        activity()->on($projectModel)->tap(
                            function (Activity $activity) use ($projectId) {
                                $activity->log_name = 'updated';
                                $activity->subject_id = current(Hashids::decode($projectId));
                            }
                        )->log('Skip to cart atualizado no projeto ' . $project->name);

                        return response()->json(['message' => 'Skip to cart atualizado no projeto']);
                    } else {
                        return response()->json(['message' => 'Alteração permitida somente em produção!'], 400);
                    }
                } catch (Exception $e) {
                    if (method_exists($e, 'getCode') && in_array($e->getCode(), [401, 402, 403, 404])) {
                        return response()->json(
                            ['message' => 'Ocorreu um erro ao atualizar o skip to cart do projeto'],
                            400
                        );
                    }
                    report($e);

                    return response()->json(
                        ['message' => 'Ocorreu um erro ao atualizar o skip to cart do projeto'],
                        400
                    );
                }
            } else {
                return response()->json(['message' => 'Ocorreu um erro ao atualizar o skip to cart do projeto'], 400);
            }
        } else {
            return response()->json(['message' => 'Ocorreu um erro ao atualizar o skip to cart do projeto'], 400);
        }
    }
}
