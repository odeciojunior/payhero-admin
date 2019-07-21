<?php

namespace Modules\Shopify\Http\Controllers;

use Exception;
use App\Entities\Company;
use App\Entities\Project;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Entities\ShopifyIntegration;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Events\ShopifyIntegrationEvent;

class ShopifyController extends Controller
{
    /**
     * @var ShopifyService
     */
    private $shopifyService;
    /**
     * @var Project
     */
    private $projectModel;
    /**
     * @var ShopifyIntegration
     */
    private $shopifyIntegrationModel;
    /**
     * @var UserProject
     */
    private $userProjectModel;


    /**
     * @return ShopifyIntegration|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getShopifyIntegrationModel()
    {
        if (!$this->shopifyIntegrationModel) {
            $this->shopifyIntegrationModel = app(ShopifyIntegration::class);
        }

        return $this->shopifyIntegrationModel;
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
     * @return UserProject|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUserProjectModel()
    {
        if (!$this->userProjectModel) {
            $this->userProjectModel = app(UserProject::class);
        }

        return $this->userProjectModel;
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $companies = Company::where('user_id', \Auth::user()->id)->get()->toArray();

        $shopifyIntegrations = ShopifyIntegration::where('user', \Auth::user()->id)->get()->toArray();

        $projects = [];

        foreach ($shopifyIntegrations as $shopifyIntegration) {

            $project = Project::find($shopifyIntegration['project']);

            if ($project) {
                $projects[] = $project;
            }
        }

        return view('shopify::index', [
            'companies' => $companies,
            'projects'  => $projects,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $dados = $request->all();

            $shopifyIntegration = $this->getShopifyIntegrationModel()
                                       ->where('token', $dados['token'])
                                       ->first();

            if($shopifyIntegration){
                if($shopifyIntegration->status == 1){
                    return response()->json(['message' => 'Integração em andamento'], 400);
                }
                return response()->json(['message' => 'Projeto já integrado'], 400);
            }

            try {
                $urlStore = str_replace('.myshopify.com','',$dados['url_store']);
                $shopifyStoreService = $this->getShopifyService($urlStore . '.myshopify.com', $dados['token']);

                if(empty($shopifyStoreService->getClient())){
                    return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
                }
                $shopifyStoreService->getShopName();

            } catch (\Exception $e) {
                report($e);
                return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
            }

            $shopifyName = $shopifyStoreService->getShopName();
            $project     = $this->getProjectModel()->create([
                                                                'name'                       => $shopifyName,
                                                                'status'                     => $this->getProjectModel()
                                                                                                ->getEnum('status', 'approved'),
                                                                'visibility'                 => 'private',
                                                                'percentage_affiliates'      => '0',
                                                                'description'                => $shopifyName,
                                                                'invoice_description'        => $shopifyName,
                                                                'url_page'                   => 'https://' . $shopifyStoreService->getShopDomain(),
                                                                'automatic_affiliation'      => false,
                                                                'shopify_id'                 => $shopifyStoreService->getShopId(),
                                                                'boleto'                     => '1',
                                                                'installments_amount'        => '12',
                                                                'installments_interest_free' => '1',
                                                            ]);

            $shopifyIntegration = $this->getShopifyIntegrationModel()->create([
                                                                                'token'     => $dados['token'],
                                                                                'url_store' => $dados['url_store'] . '.myshopify.com',
                                                                                'user'      => auth()->user()->id,
                                                                                'project'   => $project->id,
                                                                                'status'    => 1,
                                                                            ]);

            $this->getUserProjectModel()->create([
                                                    'user'                 => auth()->user()->id,
                                                    'project'              => $project->id,
                                                    'company'              => $dados['company'],
                                                    'type'                 => 'producer',
                                                    'shipment_responsible' => true,
                                                    'permissao_acesso'     => true,
                                                    'permissao_editar'     => true,
                                                    'status'               => 'active',
                                                ]);

            event(new ShopifyIntegrationEvent($project->id, $shopifyStoreService));

            return response()->json(['message' => 'Integração em andamento. Assim que tudo estiver pronto você será avisado(a)!'], 200);

        } catch (\Exception $e) {
            Log::critical('Erro ao realizar integração com loja do shopify | ShopifyController@store');
            report($e);
            return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function undoIntegration(Request $request)
    {
        try {
            $requestData = $request->all();

            $projectId = current(Hashids::decode($requestData['project_id']));

            if ($projectId) {
                //id decriptado
                $project = $this->getProjectModel()
                                ->with(['domains', 'shopifyIntegrations', 'plans', 'plans.productsPlans', 'plans.productsPlans.getProduct', 'pixels', 'discountCoupons', 'zenviaSms', 'shippings'])
                                ->where('id', $projectId)->first();

                if (!empty($project->shopify_id)) {
                    //se for shopify, voltar as integraçoes ao html padrao
                    try {

                        foreach ($project->shopifyIntegrations as $shopifyIntegration) {
                            $shopify = $this->getShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                            $shopify->setThemeByRole('main');
                            if (!empty($shopifyIntegration->theme_html)) {
                                $shopify->setTemplateHtml($shopifyIntegration->theme_file, $shopifyIntegration->theme_html);
                            }
                            if (!empty($shopifyIntegration->layout_theme_html)) {
                                $shopify->setTemplateHtml('layout/theme.liquid', $shopifyIntegration->layout_theme_html);
                            }

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function reIntegration(Request $request)
    {
        try {
            $requestData = $request->all();

            $projectId = current(Hashids::decode($requestData['project_id']));

            if ($projectId) {
                //pega o primeiro dominio ativado
                $domain = $this->getProjectModel()
                               ->where('status', $this->getProjectModel()
                                                      ->getEnum('status', 'approved'))
                               ->first();

                if ($this->getDomainService()->verifyPendingDomains($domain->id, true)) {
                    return response()->json(['message' => 'Dns revalidado com sucesso'], 200);
                } else {
                    return response()->json(['message' => 'Não foi possível revalidar o domínio'], 400);
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


}

