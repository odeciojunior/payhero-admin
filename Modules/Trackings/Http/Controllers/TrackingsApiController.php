<?php

namespace Modules\Trackings\Http\Controllers;

use App\Exceptions\TrackingCreateException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
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
     * @param  Request  $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            if (empty($request->input('page')) || $request->input('page') == '1') {
                activity()->on(new Tracking())->tap(
                    function (Activity $activity) {
                        $activity->log_name = 'visualization';
                    }
                )->log('Visualizou tela todos os códigos de rastreios');
            }
            $trackingService = new TrackingService();

            $data = $request->all();

            if (!empty($data["date_updated"])) {
                $trackings = $trackingService->getPaginatedTrackings($data);

                return TrackingResource::collection($trackings);
            } else {
                return response()->json(['message' => 'Erro ao exibir códigos de rastreio'], 400);
            }
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
            $trackingModel = new Tracking();
            $trackingService = new TrackingService();

            $trackingId = current(Hashids::decode($id));

            activity()->on($trackingModel)->tap(
                function (Activity $activity) use ($id) {
                    $activity->log_name = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                }
            )->log('Visualizou tela detalhes do rastreamento');

            $tracking = $trackingModel->with(
                [
                    'productPlanSale.plan.project.domains',
                    'product',
                    'delivery',
                ]
            )->find($trackingId);

            $apiTracking = $trackingService->findTrackingApi($tracking);

            $postedStatus = $tracking->present()->getTrackingStatusEnum('posted');
            $checkpoints = collect();

            //objeto postado
            $checkpoints->add(
                [
                    'tracking_status_enum' => $postedStatus,
                    'tracking_status' => __(
                        'definitions.enum.tracking.tracking_status_enum.'.$tracking->present()
                            ->getTrackingStatusEnum($postedStatus)
                    ),
                    'created_at' => Carbon::parse($tracking->created_at)->format('d/m/Y'),
                    'event' => 'Código de rastreio informado',
                ]
            );

            $checkpointsApi = $trackingService->getCheckpointsApi($tracking, $apiTracking);

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
            $trackingModel = new Tracking();
            $trackingService = new TrackingService();

            $tracking = $trackingModel->where('tracking_code', $trackingCode)->first();

            if (!empty($tracking)) {
                $apiTracking = $trackingService->findTrackingApi($tracking);

                $postedStatus = $tracking->present()->getTrackingStatusEnum('posted');
                $checkpoints = collect();

                //objeto postado
                $checkpoints->add(
                    [
                        'tracking_status_enum' => $postedStatus,
                        'tracking_status' => __(
                            'definitions.enum.tracking.tracking_status_enum.'.$tracking->present()
                                ->getTrackingStatusEnum($postedStatus)
                        ),
                        'created_at' => Carbon::parse($tracking->created_at)->format('d/m/Y'),
                        'event' => 'Objeto postado. As informações de rastreio serão atualizadas nos próximos dias.',
                    ]
                );

                $checkpointsApi = $trackingService->getCheckpointsApi($tracking, $apiTracking);

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
     * @param  Request  $request
     * @return JsonResponse
     */
    public function resume(Request $request)
    {
        try {
            $trackingService = new TrackingService();

            $data = $request->all();

            if (!empty($data["date_updated"])) {
                $resume = $trackingService->getResume($data);

                return response()->json(
                    [
                        'data' => $resume,
                    ]
                );
            } else {
                return response()->json(['message' => 'Erro ao exibir resumo dos rastreamentos'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao exibir resumo dos rastreamentos (TrackingApiController - resume)');
            report($e);

            return response()->json(['message' => 'Erro ao exibir resumo dos rastreamentos'], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getBlockedBalance()
    {
        try {
            $salesModel = new Sale();
            $companiesModel = new Company();

            $userAccountOwnerId = auth()->user()->account_owner_id;

            $userCompanies = $companiesModel->where('user_id', $userAccountOwnerId)
                ->pluck('id')
                ->toArray();

            $blockedBalance = $salesModel->select(
                DB::raw('sum(transactions.value) / 100 as total'),
                DB::raw('count(distinct transactions.sale_id) as sales')
            )
                ->join('transactions', 'transactions.sale_id', '=', 'sales.id')
                ->where('sales.owner_id', $userAccountOwnerId)
                ->where('sales.status', $salesModel->present()->getStatus('approved'))
                ->where('transactions.release_date', '<=', Carbon::now()->format('Y-m-d'))
                ->whereNull('transactions.invitation_id')
                ->whereIn('transactions.company_id', $userCompanies)
                ->whereDoesntHave('tracking')
                ->first();

            return response()->json(
                [
                    'total' => number_format($blockedBalance->total, 2, ',', '.'),
                    'sales' => $blockedBalance->sales,
                ]
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(
                ['message' => 'Erro ao obter saldo bloqueado por códigos de rasteio não informados'],
                400
            );
        }
    }

    /**
     * @param  TrackingStoreRequest  $request
     * @return JsonResponse
     */
    public function store(TrackingStoreRequest $request)
    {
        try {
            $data = $request->all();
            $productPlanSaleModel = new ProductPlanSale();
            $trackingModel = new Tracking();
            $trackingService = new TrackingService();

            if (!empty($data['tracking_code']) && !empty($data['product_plan_sale_id'])) {
                $ppsId = current(Hashids::decode($data['product_plan_sale_id']));
                if ($ppsId) {
                    $productPlanSale = $productPlanSaleModel->with(['tracking', 'sale.delivery'])
                        ->find($ppsId);

                    $tracking = $trackingService->createOrUpdateTracking($data['tracking_code'], $productPlanSale, true,
                        true);

                    return response()->json([
                        'message' => 'Código de rastreio salvo',
                        'data' => [
                            'id' => Hashids::encode($tracking->id),
                            'tracking_code' => $tracking->tracking_code,
                            'tracking_status_enum' => $tracking->tracking_status_enum,
                            'tracking_status' => Lang::get('definitions.enum.tracking.tracking_status_enum.'.$trackingModel->present()
                                    ->getTrackingStatusEnum($tracking->tracking_status_enum)),
                        ],
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Erro ao salvar código de rastreio',
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => 'Erro ao salvar código de rastreio',
                ], 400);
            }
        } catch (TrackingCreateException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar alterar código de rastreio (TrackingApiController - store)');
            report($e);

            return response()->json(['message' => 'Erro ao salvar código de rastreio'], 400);
        }
    }

    /**
     * @param $trackingId
     * @return JsonResponse
     */
    public function notifyClient($trackingId)
    {
        try {
            $trackingModel = new Tracking();
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
     * @param  Request  $request
     * @return JsonResponse
     * @see https://docs.laravel-excel.com/3.1/exports/queued.html
     */
    public function export(Request $request)
    {
        try {
            activity()->tap(function (Activity $activity) {
                $activity->log_name = 'created';
            })->log('Exportou código de rastreio');

            $data = $request->all();

            $user = auth()->user();

            $filename = 'trackings_report_'.Hashids::encode($user->id).'.'.$data['format'];

            (new TrackingsReportExport($data, $user, $filename))->queue($filename);

            return response()->json(['message' => 'A exportação começou', 'email' => $user->email]);
        } catch (Exception $e) {
            Log::warning('Erro ao exportar códigos de rastreio (TrackingApiController - export)');
            report($e);

            return response()->json(['message' => 'Erro ao exportar dos rastreamentos'], 400);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function import(Request $request)
    {
        try {
            activity()->tap(function (Activity $activity) {
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
