<?php

namespace Modules\Trackings\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\TrackingHistory;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Trackings\Exports\TrackingsReportExport;
use Modules\Trackings\Imports\TrackingsImport;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\TrackingService;
use Modules\Trackings\Http\Requests\TrackingStoreRequest;
use Modules\Trackings\Transformers\TrackingResource;
use Modules\Trackings\Transformers\TrackingShowResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            if (empty($request->input('page')) || $request->input('page') == '1') {

                activity()->on(new Tracking())->tap(function(Activity $activity) {
                    $activity->log_name = 'visualization';
                })->log('Visualizou tela todos os códigos de rastreios');
            }
            $trackingService = new TrackingService();

            $data = $request->all();

            $trackings = $trackingService->getPaginatedTrackings($data);

            return TrackingResource::collection($trackings);
        } catch (Exception $e) {
            Log::warning('Erro ao exibir códigos de rastreio (TrackingApiController - index)');
            report($e);

            return response()->json(['message' => 'Erro ao exibir códigos de rastreio'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse|TrackingShowResource
     */
    public function show($id)
    {
        try {
            $trackingModel   = new Tracking();
            $trackingService = new TrackingService();

            $trackingId = current(Hashids::decode($id));

            activity()->on($trackingModel)->tap(function(Activity $activity) use ($id) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::decode($id));
            })->log('Visualizou tela detalhes do rastreamento');

            $tracking = $trackingModel->with([
                                                 'product.project.domains',
                                                 'delivery',
                                             ])->find($trackingId);

            $apiTracking = $trackingService->findTrackingApi($tracking);

            $postedStatus = $tracking->present()->getTrackingStatusEnum('posted');
            $checkpoints  = collect();

            //objeto postado
            $checkpoints->add([
                                  'tracking_status_enum' => $postedStatus,
                                  'tracking_status'      => __('definitions.enum.tracking.tracking_status_enum.' . $tracking->present()
                                                                                                                            ->getTrackingStatusEnum($postedStatus)),
                                  'created_at'           => Carbon::parse($tracking->created_at)->format('d/m/Y'),
                                  'event'                => 'Código de rastreio informado',
                              ]);

            $checkpointsApi = $trackingService->getCheckpointsApi($apiTracking);

            $checkpoints = $checkpoints->merge($checkpointsApi);

            $tracking->checkpoints = $checkpoints->unique()->sortKeysDesc()->toArray();

            return new TrackingShowResource($tracking);
        } catch (Exception $e) {
            Log::warning('Erro ao exibir detalhes do código de rastreio (TrackingApiController - show)');
            report($e);

            return response()->json(['message' => 'Erro ao exibir detalhes do código de rastreio'], 400);
        }
    }

    /**
     * @param $trackingCode
     * @return JsonResponse|TrackingShowResource
     * Rota pública acessada pelo arquivo tracking.php na pasta /public
     */
    public function detail($trackingCode)
    {
        try {
            $trackingModel   = new Tracking();
            $trackingService = new TrackingService();

            $tracking = $trackingModel->where('tracking_code', $trackingCode)->first();

            if(!empty($tracking)) {
                $apiTracking = $trackingService->findTrackingApi($tracking);

                $postedStatus = $tracking->present()->getTrackingStatusEnum('posted');
                $checkpoints = collect();

                //objeto postado
                $checkpoints->add([
                    'tracking_status_enum' => $postedStatus,
                    'tracking_status' => __('definitions.enum.tracking.tracking_status_enum.' . $tracking->present()
                            ->getTrackingStatusEnum($postedStatus)),
                    'created_at' => Carbon::parse($tracking->created_at)->format('d/m/Y'),
                    'event' => 'Objeto postado. As informações de rastreio serão atualizadas nos próximos dias.',
                ]);

                $checkpointsApi = $trackingService->getCheckpointsApi($apiTracking);

                $checkpoints = $checkpoints->merge($checkpointsApi)->unique()->sortKeysDesc()->values()->toArray();

                $trackingArray = [
                    'id' => Hashids::encode($tracking->id),
                    'tracking_code' => $tracking->tracking_code,
                    'tracking_status_enum' => $tracking->tracking_status_enum,
                    'checkpoints' => $checkpoints,
                ];

                return response()->json(['data' => $trackingArray]);
            } else {
                return response()->json(['message' => 'Erro ao exibir detalhes do código de rastreio'], 400);
            }

        } catch (Exception $e) {
            Log::warning('Erro ao exibir detalhes do código de rastreio (TrackingApiController - show)');
            report($e);

            return response()->json(['message' => 'Erro ao exibir detalhes do código de rastreio'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resume(Request $request)
    {
        try {
            $trackingService = new TrackingService();

            $data = $request->all();

            $productPlanSales = $trackingService->getAllTrackings($data);

            $total            = $productPlanSales->count();
            $posted           = 0;
            $dispatched       = 0;
            $delivered        = 0;
            $out_for_delivery = 0;
            $exception        = 0;
            $unknown          = 0;

            foreach ($productPlanSales as $productPlanSale) {

                $tracking = $productPlanSale->tracking;

                if (isset($tracking)) {
                    switch ($tracking->tracking_status_enum) {
                        case $tracking->present()->getTrackingStatusEnum('posted'):
                            $posted++;
                            break;
                        case $tracking->present()->getTrackingStatusEnum('dispatched'):
                            $dispatched++;
                            break;
                        case $tracking->present()->getTrackingStatusEnum('delivered'):
                            $delivered++;
                            break;
                        case $tracking->present()->getTrackingStatusEnum('out_for_delivery'):
                            $out_for_delivery++;
                            break;
                        case $tracking->present()->getTrackingStatusEnum('exception'):
                            $exception++;
                            break;
                    }
                } else {
                    $unknown++;
                }
            }

            return response()->json([
                                        'data' => [
                                            'total'            => $total,
                                            'posted'           => $posted,
                                            'dispatched'       => $dispatched,
                                            'delivered'        => $delivered,
                                            'out_for_delivery' => $out_for_delivery,
                                            'exception'        => $exception,
                                            'unknown'          => $unknown,
                                        ],
                                    ]);
        } catch (Exception $e) {
            Log::warning('Erro ao exibir resumo dos rastreamentos (TrackingApiController - resume)');
            report($e);

            return response()->json(['message' => 'Erro ao exibir resumo dos rastreamentos'], 400);
        }
    }

    /**
     * @param TrackingStoreRequest $request
     * @return JsonResponse
     */
    public function store(TrackingStoreRequest $request)
    {
        try {
            $data                 = $request->all();
            $productPlanSaleModel = new ProductPlanSale();
            $trackingModel        = new Tracking();
            $trackingService      = new TrackingService();

            if (!empty($data['tracking_code']) && !empty($data['sale_id']) && !empty($data['product_id'])) {
                $saleId    = current(Hashids::connection('sale_id')->decode($data['sale_id']));
                $productId = current(Hashids::decode($data['product_id']));
                if ($saleId && $productId) {
                    $productPlanSale = $productPlanSaleModel->with(['tracking', 'sale.plansSales.plan.productsPlans', 'sale.delivery'])
                                                            ->where([['sale_id', $saleId], ['product_id', $productId]])
                                                            ->first();

                    $tracking = $productPlanSale->tracking;

                    //create
                    if (!isset($tracking)) {

                        $tracking = $trackingService->createTracking($data['tracking_code'], $productPlanSale);

                        if ($tracking) {

                            $trackingService->sendTrackingToApi($tracking);

                            return response()->json([
                                                        'message' => 'Código de rastreio salvo',
                                                        'data'    => [
                                                            'tracking_code'   => $tracking->tracking_code,
                                                            'tracking_status_enum' => $tracking->tracking_status_enum,
                                                            'tracking_status' => Lang::get('definitions.enum.tracking.tracking_status_enum.' . $trackingModel->present()
                                                                                                                                                             ->getTrackingStatusEnum($tracking->tracking_status_enum)),
                                                        ],
                                                    ], 200);
                        } else {
                            return response()->json([
                                                        'message' => 'Erro ao salvar código de rastreio',
                                                    ], 400);
                        }
                        //update
                    } else {
                        $trackingStatus = $tracking->tracking_status_enum;

                        $trackingCodeupdated = $tracking->update([
                                                                     'tracking_code' => $data['tracking_code'],
                                                                 ]);
                        if ($trackingCodeupdated) {

                            $trackingService->sendTrackingToApi($tracking);

                            return response()->json([
                                                        'message' => 'Código de rastreio alterado',
                                                        'data'    => [
                                                            'tracking_code'   => $tracking->tracking_code,
                                                            'tracking_status_enum' => $tracking->tracking_status_enum,
                                                            'tracking_status' => Lang::get('definitions.enum.tracking.tracking_status_enum.' . $trackingModel->present()
                                                                                                                                                             ->getTrackingStatusEnum($tracking->tracking_status_enum)),
                                                        ],
                                                    ], 200);
                        }
                    }
                }
            } else {
                return response()->json([
                                            'message' => 'Erro ao salvar código de rastreio',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar alterar código de rastreio (TrackingApiController - store)');
            report($e);

            return response()->json(['message' => 'Erro ao salvar código de rastreio'], 400);
        }

        return response()->json([], 200);
    }

    /**
     * @param $trackingId
     * @return JsonResponse
     */
    public function notifyClient($trackingId)
    {
        try {
            $trackingModel  = new Tracking();
            $productService = new ProductService();

            if (isset($trackingId)) {

                $tracking = $trackingModel->with('sale')
                                          ->find(current(Hashids::decode($trackingId)));

                if ($tracking && $tracking->sale) {

                    $saleProducts = $productService->getProductsBySale($tracking->sale);
                    event(new TrackingCodeUpdatedEvent($tracking->sale, $tracking, $saleProducts));

                    return response()->json(['message' => 'Notificação enviada com sucesso']);
                } else {
                    return response()->json(['message' => 'Erro ao notificar cliente'], 400);
                }
            } else {
                return response()->json(['message' => 'Erro ao notificar cliente'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao notificar cliente (TrackingApiController - notifyClient)');
            report($e);

            return response()->json(['message' => 'Erro ao notificar cliente'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @see https://docs.laravel-excel.com/3.1/exports/queued.html
     */
    public function export(Request $request)
    {
        try {
            activity()->tap(function(Activity $activity) {
                $activity->log_name = 'created';
            })->log('Exportou código de rastreio');

            $data = $request->all();

            $user = auth()->user();
            $user->isManager = session('isManager', false);

            $filename = 'trackings_report_' . Hashids::encode($user->id) . '.' . $data['format'];

            (new TrackingsReportExport($data, $user, $filename))->queue($filename);

            return response()->json(['message' => 'A exportação começou', 'email' => $user->email]);
        } catch (Exception $e) {
            Log::warning('Erro ao exportar códigos de rastreio (TrackingApiController - export)');
            report($e);

            return response()->json(['message' => 'Erro ao exportar dos rastreamentos'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request)
    {
        try {
            activity()->tap(function(Activity $activity) {
                $activity->log_name = 'created';
            })->log('Importou código de rastreio');

            if ($request->hasFile('import_xlsx')) {
                $extension = strtolower(request()->file('import_xlsx')->getClientOriginalExtension());
                if (in_array($extension, ['csv', 'xlsx'])) {
                    $user = auth()->user();
                    Excel::queueImport(new TrackingsImport($user), request()->file('import_xlsx'));

                    return response()->json(['message' => 'A importação começou! Você receberá uma notificação quando tudo estiver pronto!']);
                } else {
                    return response()->json(['message' => 'Formato de arquivo inválido!'], 400);
                }
            }

            return response()->json(['message' => 'Arquivo inválido'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao importar códigos de rastreio (TrackingApiController - import)');
            report($e);

            return response()->json(['message' => 'Erro ao importar arquivo'], 400);
        }
    }
}
