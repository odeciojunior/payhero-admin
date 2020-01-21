<?php

namespace Modules\Core\Services;

use Illuminate\Support\Carbon;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Vinkla\Hashids\Facades\Hashids;

class TrackingService
{

    public function sendTrackingToApi($tracking)
    {
        $trackingmoreService = new TrackingmoreService();

        return $trackingmoreService->createTracking($tracking->tracking_code);
    }

    public function findTrackingApi(Tracking $tracking)
    {
        $trackingmoreService = new TrackingmoreService();

        $response =  $trackingmoreService->getAllTrackings(['numbers' => $tracking->tracking_code]);

        $apiTracking = $response->data->items[0] ?? null;

        if(isset($apiTracking->status)){
            $status = $this->parseStatusApi($apiTracking->status);
            if($tracking->tracking_status_enum != $status){
                $tracking->tracking_status_enum = $status;
                $tracking->save();
            }
        }

        return $apiTracking;
    }

    public function parseStatusApi($status)
    {
        $trackingmoreService = new TrackingmoreService();

        return $trackingmoreService->parseStatus($status);
    }

    public function getCheckpointsApi($apiTracking)
    {
        $trackingModel = new Tracking();

        $apiCheckpoints = array_reverse($apiTracking->destination_info->trackinfo ?? []);

        $checkpoints = collect();

        if(!empty($apiCheckpoints)) {
            foreach ($apiCheckpoints as $log){

                $event = $log->Details ? $log->StatusDescription . ' - ' . $log->Details : $log->StatusDescription;

                if(!empty($event)) {

                    $status_enum = $this->parseStatusApi($log->checkpoint_status);
                    $status = $status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $trackingModel->present()->getTrackingStatusEnum($status_enum)) : 'Não informado';

                    //remove caracteres chineses e informações indesejadas
                    preg_match('/[^\p{Latin}[:punct:]\s+]/u', $event, $nonLatinChars);

                    $checkpoints->add([
                        'tracking_status_enum' => $status_enum,
                        'tracking_status' => $status,
                        'created_at' => Carbon::parse($log->Date)->format('d/m/Y'),
                        'event' => $nonLatinChars ? 'Encomenda em movimentação no exterior' :  $event,
                    ]);
                }
            }
        }

        return $checkpoints;
    }

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
            $salePresenter->getStatus('charge_back'),
        ];

        $productPlanSales = $productPlanSaleModel
            ->with([
                'tracking',
                'sale.plansSales.plan.productsPlans',
                'sale.delivery',
                'sale.client',
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

    public function getPaginatedTrackings($filters)
    {
        $productPlanSales = $this->getTrackingsQueryBuilder($filters);

        return $productPlanSales->orderBy('id', 'desc')->paginate(10);
    }

    public function getAllTrackings($filters)
    {
        $productPlanSales = $this->getTrackingsQueryBuilder($filters);

        return $productPlanSales->get();
    }
}
