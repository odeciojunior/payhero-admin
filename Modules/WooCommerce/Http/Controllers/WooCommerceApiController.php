<?php

namespace Modules\WooCommerce\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\WooCommerceService;
use Modules\WooCommerce\Transformers\WooCommerceResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ProjectService;
use Illuminate\Support\Facades\Log;
use Modules\WooCommerce\Transformers\WooCommerceIntegrationsResource;

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
            $woocommerceIntegrations = $woocommerceIntegrationModel->where(
                'user_id',
                auth()->user()->account_owner_id
            )->get();
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
            report($e);
            return response()->json(['message' => 'Ocorreu algum erro!'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $projectModel = new Project();
            $shippingModel = new Shipping();
            $userProjectModel = new UserProject();
            $woocommerceIntegrationModel = new WooCommerceIntegration();

            $projectService = new ProjectService();
            $projectNotificationService = new ProjectNotificationService();

            $dataRequest = $request->all();

            if (strlen($dataRequest['token_user']) < 10 || strlen($dataRequest['token_user']) > 100) {
                return response()->json(['message' => 'O token deve ter entre 10 e 100 letras e números!'], 400);
            }

            if (!auth()->user()->account_is_approved) {
                return response()->json(['message' => 'Finalize seu cadastro para integrar com WooCommerce'], 400);
            }

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
                ->where('token_user', $dataRequest['token_user'])->first();

            if ($woocommerceIntegration) {
                if ($woocommerceIntegration->status == 1) {
                    return response()->json(['message' => 'Integração em andamento'], 400);
                }
                return response()->json(['message' => 'Projeto já integrado'], 400);
            }

            try {
                $urlStore = $dataRequest['url_store'];
                $woocommerceService = new WooCommerceService(
                    $urlStore,
                    $dataRequest['token_user'],
                    $dataRequest['token_pass'],
                );

                if (!$woocommerceService->test_url()) {
                    return response()->json(
                        [
                            'message' => 'Url do woocommerce inválida, revise os dados informados!'
                        ],
                        400
                    );
                }
            } catch (Exception $e) {
                report($e);
            }

            if (!$woocommerceService->verifyPermissions()) {
                return response()->json(
                    [
                        'message' => 'Chave de acesso incorreta ou sem permissão de escrita!'
                    ],
                    400
                );
            }

            $woocommerceName = $urlStore;
            $company = Company::find(current(Hashids::decode($dataRequest['company'])));
            $has_pix_key = ($company && $company->has_pix_key == true) ? 1 : 0;

            try {
                $projectCreated = $projectModel->create(
                    [
                        'name' => $woocommerceName,
                        'status' => $projectModel->present()->getStatus('active'),
                        'visibility' => 'private',
                        'pix' => $has_pix_key,
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
                        'notazz_configs' => json_encode(
                            [
                                'cost_currency_type' => 1,
                                //'update_cost_woocommerce' => 1
                            ]
                        )
                    ]
                );
            } catch (Exception  $e) {
                report($e);
            }

            if (empty($projectCreated->id)) {
                return response()->json(
                    ['message' => 'Problema ao criar integração, tente novamente mais tarde!'],
                    400
                );
            }

            $projectNotificationService->createProjectNotificationDefault($projectCreated->id);
            $projectService->createUpsellConfig($projectCreated->id);

            $shippingCreated = $shippingModel->create(
                [
                    'project_id' => $projectCreated->id,
                    'name' => 'Frete gratis',
                    'information' => 'de 15 até 30 dias',
                    'value' => '0,00',
                    'type' => 'static',
                    'type_enum' => $shippingModel->present()->getTypeEnum('static'),
                    'status' => '1',
                    'pre_selected' => '1',
                    'apply_on_plans' => '["all"]',
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
                    'url_store' => $urlStore,
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

            try {
                
                $woocommerceService->fetchProducts($woocommerceIntegrationCreated->project_id, $woocommerceIntegrationCreated->user_id);
                
                $woocommerceIntegrationCreated->update(
                    [
                        'status' => 2,
                    ]
                );

                $hashedProjectId = Hashids::encode($woocommerceIntegrationCreated->project_id);

                $woocommerceService->createHooks($hashedProjectId);

            } catch (Exception $e) {
                $woocommerceIntegrationCreated->delete();
                $shippingCreated->delete();
                $projectCreated->delete();

                report($e);

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
        $doProducts = $request->opt_prod;
        $doTrackingCodes = $request->opt_track;

        $projectId = current(Hashids::decode($request->projectId));
        
        $integration = WooCommerceIntegration::where('project_id', $projectId)->first();

        $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);
        
        return $service->syncProducts($projectId, $integration, $doProducts, $doTrackingCodes);
        
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
}
