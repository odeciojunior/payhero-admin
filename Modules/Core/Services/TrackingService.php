<?php

namespace Modules\Core\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Events\CheckSaleHasValidTrackingEvent;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use stdClass;
use Vinkla\Hashids\Facades\Hashids;

class TrackingService
{
    /**
     * @param $trackingCode
     * @return mixed|null
     */
    public function sendTrackingToApi($trackingCode)
    {
        if (!empty($trackingCode)) {
            $trackingmoreService = new TrackingmoreService();
            return $trackingmoreService->createTracking($trackingCode);
        } else {
            return null;
        }
    }

    /**
     * @param Tracking $tracking
     * @param bool $refresh
     * @return mixed
     * @throws PresenterException
     */
    public function findTrackingApi(Tracking $tracking, $refresh = false)
    {
        $trackingModel = new Tracking();
        $trackingmoreService = new TrackingmoreService();

        $trackingCode = $tracking->tracking_code;

        $apiTracking = $trackingmoreService->find($trackingCode);

        $collection = $refresh
            ? $trackingModel->with(['productPlanSale'])
                ->where('tracking_code', $trackingCode)
                ->where('id', '!=', $tracking->id)
                ->get()
            : collect();
        $collection->push($tracking);

        $status = $this->parseStatusApi($apiTracking->status);
        foreach ($collection as $item) {
            if (isset($apiTracking->status)) {
                if ($item->tracking_status_enum != $status) {
                    $item->tracking_status_enum = $status;
                }
            }
            if ($refresh && !in_array($item->system_status_enum, [
                    $trackingModel->present()->getSystemStatusEnum('checked_manually'),
                ])) {
                $item->system_status_enum = $this->getSystemStatus($trackingCode, $apiTracking,
                    $item->productPlanSale);
            }

            if ($item->isDirty()) {
                $item->save();
                event(new CheckSaleHasValidTrackingEvent($item->sale_id));
            }
        }

        return $apiTracking;
    }

    /**
     * @param Tracking $tracking
     * @param $apiTracking
     * @return Collection
     * @throws PresenterException
     */
    public function getCheckpointsApi(Tracking $tracking, $apiTracking)
    {
        $checkpoints = collect();

        $apiCheckpoints = array_reverse($apiTracking->origin_info->trackinfo ?? []);
        $apiCheckpoints += array_reverse($apiTracking->destination_info->trackinfo ?? []);

        if (!empty($apiCheckpoints)) {
            foreach ($apiCheckpoints as $log) {
                $event = $log->Details ? $log->StatusDescription . ' - ' . $log->Details : $log->StatusDescription;

                if (!empty($event)) {
                    $status_enum = $this->parseStatusApi($log->checkpoint_status ?? '');
                    $status = $status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $tracking->present()->getTrackingStatusEnum($status_enum)) : 'Não informado';

                    //remove caracteres chineses e informações indesejadas
                    $blacklistWords = [
                        'asendia',
                        'beijing',
                        'chaozhou',
                        'china',
                        'dianhua',
                        'dongguan',
                        'fuyang',
                        'guangdongshengguangzhoushi',
                        'hang kong',
                        'hong kong',
                        'hongkong',
                        'jangxi',
                        'jinhua',
                        'jiangxi',
                        'jingwaijinkou',
                        'kulitiba',
                        'nanchang',
                        'shanghai',
                        'shantou',
                        'shanzhao',
                        'sheng',
                        'shenzhen',
                        'singapore',
                        'sunyou',
                        'xinyu',
                        'yanwen',
                        'yingshangxian',
                        'yiwu',
                        'zhongxin',
                    ];

                    if (Str::contains(strtolower($event),
                            $blacklistWords) || preg_match('/[^\p{Common}\p{Latin}]+/u', $event)) {
                        $event = 'Encomenda em movimentação no exterior';
                    }

                    $checkpoints->add([
                        'tracking_status_enum' => $status_enum,
                        'tracking_status'      => $status,
                        'created_at'           => Carbon::parse($log->Date)->format('d/m/Y'),
                        'event'                => $event,
                    ]);
                }
            }
        }

        return $checkpoints;
    }

    /**
     * @param $status
     * @return int
     * @throws PresenterException
     */
    public function parseStatusApi($status)
    {
        $trackingmoreService = new TrackingmoreService();

        return $trackingmoreService->parseStatus($status);
    }

    /**
     * @param string $trackingCode
     * @param stdClass|null $apiResult
     * @param ProductPlanSale $productPlanSale
     * @return false|int|mixed|string
     * @throws PresenterException
     */
    private function getSystemStatus(string $trackingCode, ?stdClass $apiResult, ProductPlanSale $productPlanSale)
    {
        $trackingModel = new Tracking();
        $salesModel = new Sale();
        $systemStatusEnum = $trackingModel->present()->getSystemStatusEnum('valid');
        if (!empty($apiResult)) {
            //verifica se a data de postagem na transportadora é menor que a data da venda
            if (!empty($apiResult->origin_info->trackinfo ?? [])) {
                $postDate = Carbon::parse($apiResult->origin_info->ItemReceived);
                if ($postDate->lt($productPlanSale->created_at)) {
                    $systemStatusEnum = $trackingModel->present()->getSystemStatusEnum('posted_before_sale');
                }
            } else {
                $systemStatusEnum = $trackingModel->present()->getSystemStatusEnum('no_tracking_info');
            }
        } else {
            $systemStatusEnum = $trackingModel->present()->getSystemStatusEnum('unknown_carrier');
        }

        //verifica se já tem uma venda nessa conta com o mesmo código de rastreio
        $sale = $productPlanSale->sale;
        $existsQuery = $salesModel->whereHas('tracking',
            function ($query) use ($trackingModel, $trackingCode, $productPlanSale) {
                $query->where('tracking_code', $trackingCode)
                    ->where('system_status_enum', '!=', $trackingModel->present()->getSystemStatusEnum('duplicated'));
            })->where('id', '!=', $sale->id)
            ->where('upsell_id', '!=', $sale->id);

        if (!empty($sale->upsell_id)) {
            $existsQuery->where('upsell_id', '!=', $sale->upsell_id)
                ->where('id', '!=', $sale->upsell_id);
        }

        $exists = $existsQuery->where(function ($query) use ($sale) {
            $query->where('customer_id', '!=', $sale->customer_id)
                ->orWhere('delivery_id', '!=', $sale->delivery_id);
        })->whereIn('status', [
                $salesModel->present()->getStatus('approved'),
                $salesModel->present()->getStatus('in_dispute'),
            ]
        )->exists();

        if ($exists) {
            $systemStatusEnum = $trackingModel->present()->getSystemStatusEnum('duplicated');
        }

        return $systemStatusEnum;
    }

    /**
     * @param string $trackingCode
     * @param ProductPlanSale $productPlanSale
     * @param bool $logging
     * @param bool $notify
     * @return Tracking|null
     */
    public function createOrUpdateTracking(
        string $trackingCode,
        ProductPlanSale $productPlanSale,
        $logging = false,
        $notify = true
    )
    {
        try {
            $logging ? activity()->enableLogging() : activity()->disableLogging();

            $trackingCode = preg_replace('/[^a-zA-Z0-9]/', '', $trackingCode);

            $apiResult = $this->sendTrackingToApi($trackingCode);

            $statusEnum = $this->parseStatusApi($apiResult->status ?? '');

            $systemStatusEnum = $this->getSystemStatus($trackingCode, $apiResult, $productPlanSale);

            $commonAttributes = [
                'sale_id'              => $productPlanSale->sale_id,
                'product_id'           => $productPlanSale->product_id,
                'product_plan_sale_id' => $productPlanSale->id,
                'amount'               => $productPlanSale->amount,
                'delivery_id'          => $productPlanSale->sale->delivery->id,
            ];

            $newAttributes = [
                'tracking_code'        => $trackingCode,
                'tracking_status_enum' => $statusEnum,
                'system_status_enum'   => $systemStatusEnum,
            ];

            $tracking = Tracking::where($commonAttributes)
                ->first();

            //atualiza e faz outras verificações caso já exista
            if (empty($tracking)) {
                $oldTracking = (object)$tracking->getAttributes();

                //atualiza
                $tracking->update($newAttributes);

                //verifica se existem duplicatas do antigo código
                if ($oldTracking->tracking_code != $trackingCode) {
                    $duplicates = Tracking::with(['productPlanSale'])
                        ->where('tracking_code', $oldTracking->tracking_code)
                        ->orderBy('id')
                        ->get();
                    //caso existam recria/revalida os códigos
                    foreach ($duplicates as $duplicate) {
                        $this->createOrUpdateTracking($duplicate->tracking_code, $duplicate->productPlanSale);
                    }
                }
            } else { //senão cria o tracking
                $tracking = Tracking::updateOrCreate($commonAttributes + $newAttributes);
            }

            if (!empty($tracking)) {
                if ($notify) event(new TrackingCodeUpdatedEvent($tracking->id));
                event(new CheckSaleHasValidTrackingEvent($productPlanSale->sale_id));
            }

            return $tracking;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    /**
     * @param $filters
     * @param int $userId
     * @return Builder
     * @throws PresenterException
     */
    public function getTrackingsQueryBuilder($filters, $userId = 0)
    {
        $trackingModel = new Tracking();
        $productPlanSaleModel = new ProductPlanSale();
        $salePresenter = (new Sale())->present();

        if (!$userId) {
            $userId = auth()->user()->account_owner_id;
        }

        $saleStatus = [
            $salePresenter->getStatus('approved'),
            $salePresenter->getStatus('in_dispute'),
        ];

        $productPlanSales = $productPlanSaleModel->with(['tracking', 'sale.delivery', 'sale.customer', 'product',]);

        $productPlanSales->whereHas('sale', function ($query) use ($filters, $saleStatus, $userId, $productPlanSales) {
            //tipo da data e periodo obrigatorio
            $dateRange = FoxUtils::validateDateRange($filters["date_updated"]);
            $query->whereBetween('end_date', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'])
                ->whereIn('status', $saleStatus)
                ->where('owner_id', $userId);

            if (isset($filters['sale'])) {
                $saleId = current(Hashids::connection('sale_id')->decode($filters['sale']));
                $query->where('id', $saleId);
            }

            //filtro transactions
            if (isset($filters['transaction_status'])) {
                $query->whereHas('transactions', function ($queryTransaction) use ($filters) {
                    $transactionPresenter = (new Transaction())->present();
                    if ($filters['transaction_status'] != 'blocked') {
                        $statusEnum = $transactionPresenter->getStatusEnum($filters['transaction_status']);
                        $queryTransaction->where('status_enum', $statusEnum);
                    } else {
                        $queryTransaction->where(function ($query) {
                            $query->where('transactions.release_date', '>', '2020-05-25') //data que começou a bloquear
                            ->orWhereHas('sale', function ($query) {
                                $query->where('is_chargeback_recovered', true);
                            });
                        })->where('transactions.release_date', '<=', Carbon::now()->format('Y-m-d'));
                    }
                    $queryTransaction->where('type', $transactionPresenter->getType('producer'))
                        ->whereNull('invitation_id');
                });
                if ($filters['transaction_status'] == 'blocked') {
                    $productPlanSales->where(function ($query) {
                        $query->whereHas('tracking', function ($trackingsQuery) {
                            $trackingPresenter = (new Tracking)->present();
                            $status = [
                                $trackingPresenter->getSystemStatusEnum('unknown_carrier'),
                                $trackingPresenter->getSystemStatusEnum('no_tracking_info'),
                                //não está bloqueado, está pendente, não transferiu pq aguarda a atualização do código
                                $trackingPresenter->getSystemStatusEnum('posted_before_sale'),
                                $trackingPresenter->getSystemStatusEnum('duplicated'),
                            ];
                            $trackingsQuery->whereIn('system_status_enum', $status);
                        })->orDoesntHave('tracking');
                    });
                }
            }
        });

        if (isset($filters['status'])) {
            if ($filters['status'] === 'unknown') {
                $productPlanSales->doesntHave('tracking');
            } else {
                $productPlanSales->whereHas(
                    'tracking',
                    function ($query) use ($trackingModel, $filters) {
                        $query->where(
                            'tracking_status_enum',
                            $trackingModel->present()->getTrackingStatusEnum($filters['status'])
                        );
                    }
                );
            }
        }

        if (isset($filters['problem'])) {
            if ($filters['problem'] == 1) {
                $productPlanSales->whereHas(
                    'tracking',
                    function ($query) use ($trackingModel, $filters) {
                        $query->whereIn('system_status_enum', [
                                $trackingModel->present()->getSystemStatusEnum('unknown_carrier'),
                                $trackingModel->present()->getSystemStatusEnum('no_tracking_info'),
                                //não está bloqueado, está pendente, não transferiu pq aguarda a atualização do código
                                $trackingModel->present()->getSystemStatusEnum('posted_before_sale'),
                                $trackingModel->present()->getSystemStatusEnum('duplicated'),
                            ]
                        );
                    }
                );
            }
        }

        if (isset($filters['tracking_code'])) {
            $productPlanSales->whereHas(
                'tracking',
                function ($query) use ($filters) {
                    $query->where('tracking_code', 'like', '%' . $filters['tracking_code'] . '%');
                }
            );
        }

        if (isset($filters['project'])) {
            $productPlanSales->whereHas(
                'product',
                function ($query) use ($filters) {
                    $query->where('project_id', current(Hashids::decode($filters['project'])));
                }
            );
        }

        $productPlanSales->whereHas('product', function ($query) {
            $query->where('type_enum', (new Product)->present()->getType('physical'));
        });

        return $productPlanSales;
    }

    /**
     * @param $filters
     * @return LengthAwarePaginator
     * @throws PresenterException
     */
    public function getPaginatedTrackings($filters)
    {
        $productPlanSales = $this->getTrackingsQueryBuilder($filters);

        return $productPlanSales->orderBy('id', 'desc')->paginate(10);
    }


    /**
     * @param $filters
     * @return array
     * @throws PresenterException
     */
    public function getResume($filters)
    {
        $trackingPresenter = (new Tracking())->present();

        $status = [
            $trackingPresenter->getTrackingStatusEnum('posted'),
            $trackingPresenter->getTrackingStatusEnum('dispatched'),
            $trackingPresenter->getTrackingStatusEnum('delivered'),
            $trackingPresenter->getTrackingStatusEnum('out_for_delivery'),
            $trackingPresenter->getTrackingStatusEnum('exception'),
        ];

        $productPlanSales = $this->getTrackingsQueryBuilder($filters)
            ->without([
                'tracking',
                'sale',
                'product',
            ])
            ->leftJoin('trackings', 'products_plans_sales.id', '=', 'trackings.product_plan_sale_id')
            ->selectRaw("COUNT(*) as total,
                                   SUM(CASE WHEN trackings.tracking_status_enum = ? THEN 1 ELSE 0 END) as posted,
                                   SUM(CASE WHEN trackings.tracking_status_enum = ? THEN 1 ELSE 0 END) as dispatched,
                                   SUM(CASE WHEN trackings.tracking_status_enum = ? THEN 1 ELSE 0 END) as delivered,
                                   SUM(CASE WHEN trackings.tracking_status_enum = ? THEN 1 ELSE 0 END) as out_for_delivery,
                                   SUM(CASE WHEN trackings.tracking_status_enum = ? THEN 1 ELSE 0 END) as exception,
                                   SUM(CASE WHEN trackings.tracking_status_enum is null THEN 1 ELSE 0 END) as unknown",
                $status)
            ->first();

        return $productPlanSales->toArray();
    }

    public function getAveragePostingTimeInPeriod(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        $gatewayIds = FoxUtils::isProduction() ? [15] : [14, 15];

        $approvedSalesWithTrackingCode = Tracking::select(DB::raw('ceil(avg(datediff(trackings.created_at, sales.end_date))) as averagePostingTime'))
            ->join('sales', 'sales.id', '=', 'trackings.sale_id')
            ->whereIn('sales.gateway_id', $gatewayIds)
            ->where('sales.payment_method', Sale::PAYMENT_TYPE_CREDIT_CARD)
            ->whereIn('sales.status', [
                Sale::STATUS_APPROVED,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_IN_DISPUTE
            ])
            ->whereBetween(
                'sales.start_date',
                [$startDate->format('Y-m-d') . ' 00:00:00', $endDate->format('Y-m-d') . ' 23:59:59']
            )
            ->where('sales.owner_id', $user->id)
            ->get();

        return $approvedSalesWithTrackingCode->toArray()[0]['averagePostingTime'] ?? null;
    }

    public function getUninformedTrackingCodeRateInPeriod(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        $saleService = new SaleService();
        $approvedSalesAmount = $saleService->getApprovedSalesInPeriod($user, $startDate, $endDate)->count();

        if ($approvedSalesAmount < 20) {
            return 7; //7% means score 6
        }

        $untrackedSalesAmount = $saleService->getApprovedSalesInPeriod($user, $startDate, $endDate)
            ->doesntHave('tracking')
            ->count();

        return round(($untrackedSalesAmount * 100 / $approvedSalesAmount), 2);
    }

    public function getTrackingCodeProblemRateInPeriod(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        $salesWithTrackingCodeProblemsAmount = Sale::where(function ($query) {
            $query->whereHas('tracking', function ($trackingsQuery) {
                $status = [
                    Tracking::SYSTEM_STATUS_UNKNOWN_CARRIER,
                    Tracking::SYSTEM_STATUS_NO_TRACKING_INFO,
                    Tracking::SYSTEM_STATUS_POSTED_BEFORE_SALE,
                    Tracking::SYSTEM_STATUS_DUPLICATED
                ];
                $trackingsQuery->whereIn('system_status_enum', $status);
            });
        })->where(function ($q) use ($user) {
            $q->where('owner_id', $user->id)
                ->orWhere('affiliate_id', $user->id);
        })->count();

        if ($salesWithTrackingCodeProblemsAmount < 20) {
            return 2; //2% means score 6
        }

        $saleService = new SaleService();
        $approvedSalesAmount = $saleService->getApprovedSalesInPeriod($user, $startDate, $endDate)->count();

        if (!$approvedSalesAmount) return 0;

        return round(($salesWithTrackingCodeProblemsAmount * 100 / $approvedSalesAmount), 2);
    }

    public static  function  getTrackingToday(User $user) {
        return Tracking::join('sales', 'sales.id', '=', 'trackings.sale_id')
            ->whereBetween(
                'trackings.created_at',
                [now()->format('Y-m-d') . ' 00:00:00', now()->format('Y-m-d') . ' 23:59:59']
            )
            ->where('sales.owner_id', $user->id)
            ->get();
    }
}
