<?php

namespace Modules\WooCommerce\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
//use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\TaskService;
use Modules\Core\Services\UserService;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\WooCommerceIntegration;
//use Modules\Core\Events\WooCommerceIntegrationEvent;
//use App\Jobs\ImportWooCommerceTrackingCodesJob;
//use Modules\Core\Services\WooCommerceErrors;
use Modules\Core\Services\WooCommerceService;
use Modules\WooCommerce\Transformers\WooCommerceResource;


use Automattic\WooCommerce\Client;

/**
 * Class ApiController
 * @package Modules\WooCommerce\Http\Controllers
 */
class WooCommerceApiController extends Controller
{
    /**
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $projectModel = new Project();
            $woocommerceIntegrationModel = new WooCommerceIntegration();
            activity()->on($woocommerceIntegrationModel)->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log('Visualizou tela todos as integrações com o woocommerce');
            
            
            
            $woocommerceIntegrations = $woocommerceIntegrationModel->where('user_id', auth()->user()->account_owner_id)->get();
            

            $projects = [];

            foreach ($woocommerceIntegrations as $woocommerceIntegration) {
                $project = $projectModel->where('id', $woocommerceIntegration->project_id)
                    ->where('status', $projectModel->present()->getStatus('active'))->first();

                if (!empty($project)) {
                    $projects[] = $project;
                }
            }

            return WooCommerceResource::collection(collect($projects));
        } catch (Exception $e) {
            // print_r($e);
            return response()->json(['message' => 'Ocorreu algum erro!'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $companyService = new CompanyService();
            $userService = new UserService();

            $dataRequest = $request->all();
            
            if (strlen($dataRequest['token_user']) < 10 || strlen($dataRequest['token_user']) > 100) {
                return response()->json(['message' => 'O token deve ter entre 10 e 100 letras e números!'], 400);
            }

            $companyDocumentPending = $companyService->haveAnyDocumentPending();
            $userDocumentPending = $userService->haveAnyDocumentPending();
            if ($companyDocumentPending || $userDocumentPending) {
                return response()->json(['message' => 'Finalize seu cadastro para integrar com WooCommerce'], 400);
            }

            $projectModel = new Project();
            $userProjectModel = new UserProject();
            $woocommerceIntegrationModel = new WooCommerceIntegration();
            $shippingModel = new Shipping();


            if (empty($dataRequest['company'])) {
                return response()->json(
                    [
                        'message' => 'A empresa precisa estar aprovada transacionar para realizar a integração!'
                    ],
                    400
                );
            }

            

            $woocommerceIntegration = $woocommerceIntegrationModel
                ->where('url_store', $dataRequest['url_store'])
                ->orWhere('token_user', $dataRequest['token_user'])->first();
            
            if ($woocommerceIntegration) {
                
                
                if ($woocommerceIntegration->status == 1) {
                    return response()->json(['message' => 'Integração em andamento'], 400);
                }

                return response()->json(['message' => 'Projeto já integrado'], 400);
            }

            try {
                $dataRequest['url_store'] = $dataRequest['url_store'];
                

                $urlStore = $dataRequest['url_store'];
                

                $woocommerceService = new WooCommerceService($urlStore , $dataRequest['token_user'], $dataRequest['token_pass'], );

                if (!$woocommerceService->test_url()) {
                    return response()->json(
                        [
                            'message' => 'Url do woocommerce inválida, revise os dados informados!'
                        ],
                        400
                    );
                }
            } catch (Exception $e) {
                
            }
            
            if(!$woocommerceService->verifyPermissions()){
                
                return response()->json(
                    [
                        'message' => 'Problema ao testar a chave de acesso!'
                    ],
                    400
                );
            }
            

            
            $woocommerceName = $urlStore;
            
            try{
                $projectCreated = $projectModel->create(
                    [
                        'name' => $woocommerceName,
                        'status' => $projectModel->present()->getStatus('active'),
                        'visibility' => 'private',
                        'percentage_affiliates' => '0',
                        'description' => $woocommerceName,
                        'invoice_description' => $woocommerceName,
                        'url_page' => $urlStore,
                        'automatic_affiliation' => false,
                        'woocommerce_id' => '1',
                        'boleto' => '1',
                        'installments_amount' => '12',
                        'installments_interest_free' => '1',
                        'checkout_type' => 2, // checkout de 1 passo
                        'notazz_configs' => json_encode([
                            'cost_currency_type' => 1,
                            //'update_cost_woocommerce' => 1
                        ])
                    ]
                );
               

            }catch (Exception  $e) {
               
            }

            if (empty($projectCreated->id)) {
                return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde!'], 400);
            }

            $shippingCreated = $shippingModel->create(
                [
                    'project_id'         => $projectCreated->id,
                    'name'               => 'Frete gratis',
                    'information'        => 'de 15 até 30 dias',
                    'value'              => '0,00',
                    'type'               => 'static',
                    'type_enum'          => $shippingModel->present()->getTypeEnum('static'),
                    'status'             => '1',
                    'pre_selected'       => '1',
                    'apply_on_plans'     => '["all"]',
                    'not_apply_on_plans' => '[]'
                ]
            );
            
            

            if (empty($shippingCreated->id)) {
                $projectCreated->delete();

                return response()->json(
                    [
                        'message' => 'Problema ao criar integração, tente novamente mais tarde..'
                    ],
                    400
                );
            }

            $woocommerceIntegrationCreated = $woocommerceIntegrationModel->create(
                [
                    'token_user' => $dataRequest['token_user'],
                    'token_pass' => $dataRequest['token_pass'],
                    'url_store' => $urlStore ,
                    'user_id' => auth()->user()->id,
                    'project_id' => $projectCreated->id,
                    'status' => 1,
                ]
            );

            

            if (empty($woocommerceIntegrationCreated->id)) {
                $shippingCreated->delete();
                $projectCreated->delete();

                return response()->json(
                    [
                        'message' => 'Problema ao criar integração, tente novamente mais tarde'
                    ],
                    400
                );
            }

            $userProjectCreated = $userProjectModel->create(
                [
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
                ]
            );

            if (empty($userProjectCreated)) {
                $woocommerceIntegrationCreated->delete();
                $shippingCreated->delete();
                $projectCreated->delete();

                return response()->json(
                    [
                        'message' => 'Problema ao criar integração, tente novamente mais tarde'
                    ],
                    400
                );
            }



            try{
                // $woocommerceService = new WooCommerceService($dataRequest['url_store'] , $dataRequest['token_user'], $dataRequest['token_pass'] );
                // $woocommerceService->verifyPermissions();
                $products = $woocommerceService->fetchProducts();
                
                $result = $woocommerceService->importProducts($woocommerceIntegrationCreated->project_id, $woocommerceIntegrationCreated->user_id, $products);
                
                $woocommerceIntegrationCreated->update(
                    [
                        'status' => 2,
                    ]
                );
                
                
            }catch(Exception $e) {
                $woocommerceIntegrationCreated->delete();
                $shippingCreated->delete();
                $projectCreated->delete();
                
                Log::debug($e);
                
                return response()->json(
                    [
                        'message' => 'Problema ao criar integração, tente novamente mais tarde'
                    ],
                    400
                );
            }
            
            

            return response()->json(
                [
                    'message' => 'Integração em andamento. Assim que tudo estiver pronto você será avisado(a)!'
                ],
                200
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
        }
    }

    public function synchronizeProducts(Request $request)
    {
        
    }


    public function undoIntegration(Request $request)
    {
        
    }

    public function reIntegration(Request $request)
    {
        
    }


    public function synchronizeTrackings(Request $request)
    {
        
    }

    public function updateToken(Request $request)
    {
        
    }

    public function verifyPermission(Request $request)
    {
        
    }

    public function setSkipToCart(Request $request)
    {
        
    }
}
