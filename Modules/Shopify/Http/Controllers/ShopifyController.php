<?php

namespace Modules\Shopify\Http\Controllers;

use Exception;
use App\Entities\Company;
use App\Entities\Project;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Routing\Controller;
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
                return response()->json(['message' => 'Projeto já integrado'], 400);
            }

            try {
                $shopifyService = $this->getShopifyService($dados['url_store'] . '.myshopify.com', $dados['token']);

            } catch (\Exception $e) {
                report($e);
                return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
            }

            $shopifyName = $this->getShopName();
            $project     = $this->getProjectModel()->create([
                                                                'name'                  => $shopifyName,
                                                                'status'                => $this->getProjectModel()
                                                                                                ->getEnum('status', 'approved'),
                                                                'visibility'            => 'private',
                                                                'percentage_affiliates' => '0',
                                                                'description'           => $shopifyName,
                                                                'invoice_description'   => $shopifyName,
                                                                'url_page'              => 'https://' . $this->getShopDomain(),
                                                                'automatic_affiliation' => false,
                                                                'shopify_id'            => $this->getShopId(),
                                                            ]);

            $shopifyIntegration = $this->getShopifyIntegrationModel()->create([
                                                                                'token'     => $dados['token'],
                                                                                'url_store' => $dados['url_store'] . '.myshopify.com',
                                                                                'user'      => auth()->user()->id,
                                                                                'project'   => $project->id,
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

            event(new ShopifyIntegrationEvent($project->id));

            return response()->json(['message' => 'Integração em andamento. Assim que tudo estiver pronto você será avisado(a)!'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
        }
    }

}

