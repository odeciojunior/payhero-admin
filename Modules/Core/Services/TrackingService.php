<?php

namespace Modules\Core\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Vinkla\Hashids\Facades\Hashids;

class TrackingService
{
    /**
     * @var string
     *
     * Data em que migrou da aftership/perfectlog para a trackingmore
     */
    private $migrationDate = '2020-01-25 00:00:00';

    /**
     * @param Tracking $tracking
     * @return mixed
     */
    public function sendTrackingToApi(Tracking $tracking)
    {
        $trackingmoreService = new TrackingmoreService();

        return $trackingmoreService->createTracking($tracking->tracking_code);
    }

    /**
     * @param Tracking $tracking
     * @return mixed|null
     * @throws PresenterException
     */
    public function findTrackingApi(Tracking $tracking)
    {
        //if de 10k. Busca o código de rastreio na trackingmore caso a data de criação
        //seja posterior a data da migração. Caso contrário busca as informações na
        //perfectlog/aftership
        if ($tracking->created_at->gte($this->migrationDate)) {

            $trackingmoreService = new TrackingmoreService();

            $response = $trackingmoreService->getAllTrackings(['numbers' => $tracking->tracking_code]);

            $apiTracking = $response->data->items[0] ?? null;

            if (isset($apiTracking->status)) {
                $status = $this->parseStatusApi($apiTracking->status);
                if ($tracking->tracking_status_enum != $status) {
                    $tracking->tracking_status_enum = $status;
                    $tracking->save();
                }
            }

            return $apiTracking;
        } else {
            $perfectLogService = new PerfectLogService();

            $apiTracking = $perfectLogService->find($tracking->tracking_code);

            if (isset($apiTracking->tracking_status)) {
                $status = $this->parseStatusApi($apiTracking->tracking_status, true);
                if ($tracking->tracking_status_enum != $status) {
                    $tracking->tracking_status_enum = $status;
                    $tracking->save();
                }
            }

            return $apiTracking;
        }
    }

    /**
     * @param $status
     * @param bool $beforeMigration
     * @return int|mixed
     * @throws PresenterException
     */
    public function parseStatusApi($status, $beforeMigration = false)
    {
        //if de 10k. Converte o status da trackingmore caso a data de criação
        //seja posterior a data da migração. Caso contrário converte o status
        //da perfectlog/aftership
        if (!$beforeMigration) {
            $trackingmoreService = new TrackingmoreService();

            return $trackingmoreService->parseStatus($status);
        } else {
            $perfectLogService = new PerfectLogService();

            return $perfectLogService->parseStatus($status);
        }
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

        //if de 10k. Trata os checkpoints da trackingmore caso a data de criação
        //seja posterior a data da migração. Caso contrário trata os checkpoints
        //da perfectlog/aftership
        if($tracking->created_at->gte($this->migrationDate)) {

            $apiCheckpoints = array_reverse($apiTracking->destination_info->trackinfo ?? []);

            if (!empty($apiCheckpoints)) {
                foreach ($apiCheckpoints as $log) {

                    $event = $log->Details ? $log->StatusDescription . ' - ' . $log->Details : $log->StatusDescription;

                    if (!empty($event)) {

                        $status_enum = $this->parseStatusApi($log->checkpoint_status);
                        $status = $status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $tracking->present()->getTrackingStatusEnum($status_enum)) : 'Não informado';

                        //remove caracteres chineses e informações indesejadas
                        preg_match('/[^\p{Common}\p{Latin}]+/u', $event, $nonLatinChars);

                        $checkpoints->add([
                            'tracking_status_enum' => $status_enum,
                            'tracking_status' => $status,
                            'created_at' => Carbon::parse($log->Date)->format('d/m/Y'),
                            'event' => $nonLatinChars ? 'Encomenda em movimentação no exterior' : $event,
                        ]);
                    }
                }
            }
        } else {
            if (!empty($apiTracking->trail)) {
                foreach ($apiTracking->trail as $log) {
                    $status_enum = $this->parseStatusApi($log->tracking_status, true);
                    $status = $status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $tracking->present()->getTrackingStatusEnum($status_enum)) : 'Não informado';

                    $event = $log->event;

                    //remove caracteres chineses e informações indesejadas
                    preg_match('/[^\p{Common}\p{Latin}]+/u', $event, $nonLatinChars);
                    $event = str_replace([
                        'Clique aquiMinhas Importações - ',
                        'CHINA/',
                        'CHINA /',
                        'Paísem'
                    ], '', $event);
                    $event = str_replace([
                        'de País em',
                    ], 'do exterior', $event);

                    $checkpoints->add([
                        'tracking_status_enum' => $status_enum,
                        'tracking_status' => $status,
                        'created_at' => Carbon::parse($log->updated_at)->format('d/m/Y'),
                        'event' => $nonLatinChars ? 'Encomenda em movimentação no exterior' : $event,
                    ]);
                }
            }
        }

        return $checkpoints;
    }

    /**
     * @param string $trackingCode
     * @param ProductPlanSale $productPlanSale
     * @return mixed
     * @throws PresenterException
     */
    public function createTracking(string $trackingCode, ProductPlanSale $productPlanSale)
    {
        $trackingModel = new Tracking();

        $planSale = $productPlanSale
            ->sale
            ->plansSales
            ->where('plan_id', $productPlanSale->plan_id)
            ->where('sale_id', $productPlanSale->sale_id)
            ->first();

        $productPlan = $planSale->plan
            ->productsPlans
            ->where('product_id', $productPlanSale->product_id)
            ->where('plan_id', $productPlanSale->plan_id)
            ->first();

        $amount = $productPlan->amount * $planSale->amount;

        $tracking = $trackingModel->create([
            'sale_id' => $productPlanSale->sale->id,
            'product_id' => $productPlanSale->product_id,
            'product_plan_sale_id' => $productPlanSale->id,
            'plans_sale_id' => $planSale->id,
            'amount' => $amount,
            'delivery_id' => $productPlanSale->sale->delivery->id,
            'tracking_code' => $trackingCode,
            'tracking_status_enum' => $trackingModel->present()
                ->getTrackingStatusEnum('posted'),
        ]);

        return $tracking;
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

        if(!$userId){
            $userId = auth()->user()->account_owner_id;
        }

        $saleStatus = [
            $salePresenter->getStatus('approved'),
        ];

        $productPlanSales = $productPlanSaleModel
            ->with([
                'tracking',
                'sale.plansSales.plan.productsPlans',
                'sale.delivery',
                'sale.customer',
                'product',
            ])
            ->whereHas('sale', function ($query) use ($filters, $saleStatus, $userId) {
                //tipo da data e periodo obrigatorio
                $dateRange = FoxUtils::validateDateRange($filters["date_updated"]);
                $query->whereBetween('end_date', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59'])
                    ->whereIn('status', $saleStatus)
                    ->where('owner_id', $userId);

                if (isset($filters['sale'])) {
                    $saleId = current(Hashids::connection('sale_id')->decode($filters['sale']));
                    $query->where('id', $saleId);
                }
            });

        if (isset($filters['status'])) {
            if ($filters['status'] === 'unknown') {
                $productPlanSales->doesntHave('tracking');
            } else {
                $productPlanSales->whereHas('tracking', function ($query) use ($trackingModel, $filters) {
                    $query->where('tracking_status_enum', $trackingModel->present()->getTrackingStatusEnum($filters['status']));
                });
            }
        }

        if (isset($filters['tracking_code'])) {
            $productPlanSales->whereHas('tracking', function ($query) use ($filters) {
                $query->where('tracking_code', 'like', '%' . $filters['tracking_code'] . '%');
            });
        }

        if (isset($filters['project'])) {
            $productPlanSales->whereHas('product', function ($query) use ($filters) {
                $query->where('project_id', current(Hashids::decode($filters['project'])));
            });
        }

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
                                   SUM(CASE WHEN trackings.tracking_status_enum is null THEN 1 ELSE 0 END) as unknown", $status)
            ->first();

        return $productPlanSales->toArray();
    }
}
