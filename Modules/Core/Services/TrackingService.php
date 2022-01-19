<?php

namespace Modules\Core\Services;

use App\Jobs\RevalidateTrackingDuplicateJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Events\CheckSaleHasValidTrackingEvent;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Vinkla\Hashids\Facades\Hashids;

class TrackingService
{

    public function sendTrackingToApi($trackingCode)
    {
        if (!empty($trackingCode)) {
            $trackingmoreService = new TrackingmoreService();
            return $trackingmoreService->createTracking($trackingCode);
        } else {
            return null;
        }
    }

    public function findTrackingApi(Tracking $tracking)
    {
        $trackingmoreService = new TrackingmoreService();

        $trackingCode = $tracking->tracking_code;

        return $trackingmoreService->find($trackingCode);
    }

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
                        'tracking_status' => $status,
                        'created_at' => Carbon::parse($log->Date)->format('d/m/Y'),
                        'event' => $event,
                    ]);
                }
            }
        }

        return $checkpoints;
    }

    public function parseStatusApi($status)
    {
        $trackingmoreService = new TrackingmoreService();

        return $trackingmoreService->parseStatus($status);
    }

    private function getSystemStatus(string $trackingCode, ?object $apiResult, ProductPlanSale $productPlanSale)
    {
        $systemStatusEnum = Tracking::SYSTEM_STATUS_VALID;
        if (!empty($apiResult)) {
            //verifica se a data de postagem na transportadora é menor que a data da venda
            if (!empty($apiResult->origin_info->trackinfo ?? [])) {
                $postDate = Carbon::parse($apiResult->origin_info->ItemReceived);
                if ($postDate->lt($productPlanSale->created_at)) {
                    $systemStatusEnum = Tracking::SYSTEM_STATUS_POSTED_BEFORE_SALE;
                }
            } else {
                $systemStatusEnum = Tracking::SYSTEM_STATUS_NO_TRACKING_INFO;
            }
        } else {
            $systemStatusEnum = Tracking::SYSTEM_STATUS_UNKNOWN_CARRIER;
        }

        $saleId = $productPlanSale->sale_id;
        $upsellId = $productPlanSale->upsell_id;
        $customerId = $productPlanSale->customer_id;
        $deliveryId = $productPlanSale->delivery_id;

        //verifica se já tem uma venda nessa conta com o mesmo código de rastreio
        $duplicatedQuery = DB::table('sales as d')
            ->join('trackings as t', 'd.id', '=', 't.sale_id')
            ->where('t.tracking_code', $trackingCode)
            ->where('t.system_status_enum', '!=', Tracking::SYSTEM_STATUS_DUPLICATED)
            ->where('d.id', '!=', $saleId)
            ->where(function ($query) use ($saleId) {
                $query->whereNull('d.upsell_id')
                    ->orWhere('d.upsell_id', '!=', $saleId);
            });

        if (!empty($upsellId)) {
            $duplicatedQuery->where('d.id', '!=', $upsellId)
                ->where(function ($query) use ($upsellId) {
                    $query->whereNull('d.upsell_id')
                        ->orWhere('d.upsell_id', '!=', $upsellId);
                });
        }

        $duplicatedQuery->where(function ($query) use ($deliveryId, $customerId) {
            $query->where('d.customer_id', '!=', $customerId)
                ->orWhere('d.delivery_id', '!=', $deliveryId);
        })->whereIn('d.status', [Sale::STATUS_APPROVED, Sale::STATUS_IN_DISPUTE]);

        if ($duplicatedQuery->exists()) {
            $systemStatusEnum = Tracking::SYSTEM_STATUS_DUPLICATED;
        }

        return $systemStatusEnum;
    }

    public function createOrUpdateTracking(string $trackingCode, int $productPlanSaleId, bool $logging = false, bool $notify = true): ?Tracking
    {
        try {
            $logging ? activity()->enableLogging() : activity()->disableLogging();

            $trackingCode = preg_replace('/[^a-zA-Z0-9]/', '', $trackingCode);
            $trackingCode = strtoupper($trackingCode);

            $productPlanSale = ProductPlanSale::select([
                DB::raw('products_plans_sales.*'),
                's.delivery_id',
                's.customer_id',
                's.upsell_id',
            ])->join('sales as s', 'products_plans_sales.sale_id', '=', 's.id')
                ->find($productPlanSaleId);

            $apiResult = $this->sendTrackingToApi($trackingCode);

            $statusEnum = $this->parseStatusApi($apiResult->status ?? '');

            $systemStatusEnum = $this->getSystemStatus($trackingCode, $apiResult, $productPlanSale);

            $commonAttributes = [
                'sale_id' => $productPlanSale->sale_id,
                'product_id' => $productPlanSale->product_id,
                'product_plan_sale_id' => $productPlanSale->id,
                'amount' => $productPlanSale->amount,
                'delivery_id' => $productPlanSale->sale->delivery->id,
            ];

            $newAttributes = [
                'tracking_code' => $trackingCode,
                'tracking_status_enum' => $statusEnum,
                'system_status_enum' => $systemStatusEnum,
            ];

            $tracking = Tracking::where($commonAttributes)
                ->first();

            //atualiza e faz outras verificações caso já exista
            if (!empty($tracking)) {
                $oldTracking = (object)$tracking->getAttributes();
                $oldTrackingCode = $oldTracking->tracking_code;

                //atualiza
                $tracking->fill($newAttributes);
                if ($tracking->isDirty()) {
                    $tracking->save();
                    event(new CheckSaleHasValidTrackingEvent($productPlanSale->sale_id));
                }

                if (strtoupper($oldTrackingCode) != strtoupper($trackingCode)) {
                    //verifica se existem duplicatas do antigo código
                    $duplicates = Tracking::select('product_plan_sale_id as id')
                        ->where('tracking_code', $oldTrackingCode)
                        ->get();
                    //caso existam recria/revalida os códigos
                    if ($duplicates->isNotEmpty()) {
                        RevalidateTrackingDuplicateJob::dispatch($oldTrackingCode, $duplicates->toArray());
                    }
                } else {
                    $notify = false;
                }
            } else { //senão cria um novo tracking
                $tracking = Tracking::updateOrCreate($commonAttributes + $newAttributes);
                event(new CheckSaleHasValidTrackingEvent($productPlanSale->sale_id));
            }

            if (!empty($tracking) && $notify) {
                event(new TrackingCodeUpdatedEvent($tracking->id));
            }

            return $tracking;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    public function getTrackingsQueryBuilder($filters, $userId = 0)
    {
        $productPlanSaleModel = new ProductPlanSale();
        $filters['status'] =  is_array($filters['status']) ? implode(',', $filters['status']) : $filters['status'];
        $filters['project'] =  is_array($filters['project']) ? implode(',', $filters['project']) : $filters['project'];
        $filters['transaction_status'] =  is_array($filters['transaction_status']) ? implode(',', $filters['transaction_status']) : $filters['transaction_status'];

        if (!$userId) {
            $userId = auth()->user()->account_owner_id;
        }

        $saleStatus = [
            Sale::STATUS_APPROVED,
            Sale::STATUS_IN_DISPUTE,
        ];

        $productPlanSales = $productPlanSaleModel->with([
            'tracking',
            'sale.delivery',
            'sale.customer',
            'product',
        ]);


        $projects = explode(',', $filters['project']);
        $projectsIds = collect($projects)->map(function ($project) {
            return current(Hashids::decode($project)) ?: '';
        })->toArray();

        $productPlanSales->whereHas('sale', function ($query) use ($filters, $saleStatus, $userId, $projectsIds) {
            //tipo da data e periodo obrigatorio
            $dateRange = FoxUtils::validateDateRange($filters["date_updated"]);
            $query->whereBetween('end_date', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'])
                ->whereIn('status', $saleStatus)
                ->where('owner_id', $userId);

            if (!empty($filters['sale'])) {
                $saleId = current(Hashids::connection('sale_id')->decode($filters['sale']));
                $query->where('id', $saleId);
            }


            //filtro transactions
            if (!empty($filters['transaction_status'])) {
                $filterTransaction = explode(',', $filters['transaction_status']);
                $query->whereHas('transactions', function ($qrTransaction) use ($filterTransaction)
                {
                    $transactionPresenter = (new Transaction())->present();
                    $statusEnum = [];
                    foreach($filterTransaction as $item){
                        if($item <> 'blocked'){
                            $statusEnum[] = $transactionPresenter->getStatusEnum($item);
                        }
                    }

                    if(in_array('blocked',$filterTransaction))
                    {
                        $qrTransaction->where(function ($qr) use ($statusEnum) {
                            $qr->where(function ($query) {
                                $query->where('transactions.release_date', '>', '2020-05-25') //data que começou a bloquear
                                ->orWhereHas('sale', function ($query) {
                                    $query->where('is_chargeback_recovered', true);
                                });
                            })->where('transactions.release_date', '<=', Carbon::now()->format('Y-m-d'))
                            ->where('tracking_required', true);

                            if(count($statusEnum)>0){
                                $qr->orWhereIn('status_enum', $statusEnum);
                            }
                        });

                    }else{
                        $qrTransaction->whereIn('status_enum', $statusEnum);
                    }

                    $qrTransaction->where('type', Transaction::TYPE_PRODUCER)
                    ->whereNull('invitation_id')
                    ->where('is_waiting_withdrawal', 0)
                    ->whereNull('withdrawal_id');
                });
            }

            if (!empty($projectsIds) && !in_array('', $projectsIds))
            {
                $query->whereIn('project_id', $projectsIds);
            }

        });

        if (!empty($filters['status'])) {
            $filterStatus = explode(',', $filters['status']);
            $productPlanSales->where(function ($query) use ($filterStatus) {
                $statusArray = array_reduce($filterStatus, function ($carry, $item) {
                    if ($item !== 'unknown') $carry[] = (new Tracking())->present()->getTrackingStatusEnum($item);
                    return $carry;
                }, []);

                $query->whereHas('tracking', function ($trackingQuery) use ($statusArray) {
                    $trackingQuery->whereIn('tracking_status_enum', $statusArray);
                });

                if (in_array('unknown', $filterStatus)) {
                    $query->orDoesntHave('tracking');
                }
            });
        }

        if (!empty($filters['problem'])) {
            if ($filters['problem'] == 1) {
                $productPlanSales->whereHas(
                    'tracking',
                    function ($query) {
                        $query->whereIn('system_status_enum', [
                                Tracking::SYSTEM_STATUS_UNKNOWN_CARRIER,
                                Tracking::SYSTEM_STATUS_NO_TRACKING_INFO,
                                Tracking::SYSTEM_STATUS_POSTED_BEFORE_SALE,
                                Tracking::SYSTEM_STATUS_DUPLICATED,
                            ]
                        );
                    }
                );
            }
        }

        if (!empty($filters['tracking_code'])) {
            $productPlanSales->whereHas(
                'tracking',
                function ($query) use ($filters) {
                    $query->where('tracking_code', 'like', '%' . $filters['tracking_code'] . '%');
                }
            );
        }

        $productPlanSales->where(function($q) {
            
            $q->whereHas('product', function ($query) {
                $query->where('type_enum', Product::TYPE_PHYSICAL);
            })->orWhereNull('products_plans_sales.product_id');
        
        });

        return $productPlanSales;
    }

    public function getPaginatedTrackings($filters)
    {
        $productPlanSales = $this->getTrackingsQueryBuilder($filters);

        return $productPlanSales->orderBy('id', 'desc')->paginate(10);
    }

    public function getResume($filters)
    {
        $productPlanSales = $this->getTrackingsQueryBuilder($filters)
            ->without(
                [
                    'tracking',
                    'sale',
                    'product',
                ]
            )
            ->leftJoin('trackings', 'products_plans_sales.id', '=', 'trackings.product_plan_sale_id')
            ->selectRaw(
                "COUNT(*) as total,
                                SUM(CASE WHEN trackings.tracking_status_enum = " . Tracking::STATUS_POSTED . " THEN 1 ELSE 0 END) as posted,
                                SUM(CASE WHEN trackings.tracking_status_enum = " . Tracking::STATUS_DISPATCHED . " THEN 1 ELSE 0 END) as dispatched,
                                SUM(CASE WHEN trackings.tracking_status_enum = " . Tracking::STATUS_DELIVERED . " THEN 1 ELSE 0 END) as delivered,
                                SUM(CASE WHEN trackings.tracking_status_enum = " . Tracking::STATUS_OUT_FOR_DELIVERY . " THEN 1 ELSE 0 END) as out_for_delivery,
                                SUM(CASE WHEN trackings.tracking_status_enum = " . Tracking::STATUS_EXCEPTION . " THEN 1 ELSE 0 END) as exception,
                                SUM(CASE WHEN trackings.tracking_status_enum is null THEN 1 ELSE 0 END) as unknown"
            )
            ->first();

        return $productPlanSales->toArray();
    }

    public function getAveragePostingTimeInPeriod(User $user, Carbon $startDate, Carbon $endDate): ?float
    {
        $gatewayIds = [Gateway::ASAAS_PRODUCTION_ID, Gateway::GETNET_PRODUCTION_ID];
        if(!FoxUtils::isProduction()){
            $gatewayIds = array_merge($gatewayIds, [Gateway::ASAAS_SANDBOX_ID, Gateway::GETNET_SANDBOX_ID]);
        }

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

    public static function getTrackingToday(User $user)
    {
        return Tracking::join('sales', 'sales.id', '=', 'trackings.sale_id')
            ->whereBetween(
                'trackings.created_at',
                [now()->format('Y-m-d') . ' 00:00:00', now()->format('Y-m-d') . ' 23:59:59']
            )
            ->where('sales.owner_id', $user->id)
            ->get();
    }
}
