<?php

namespace Modules\Trackings\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Events\TrackingsImportedEvent;
use Modules\Core\Services\PerfectLogService;
use Modules\Core\Services\TrackingService;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsImport implements ToCollection, WithChunkReading, ShouldQueue, WithEvents
{
    /**
     * @var User
     */
    private $user;

    /**
     * TrackingsImport constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $saleModel = new Sale();
        $trackingService = new TrackingService();
        $perfectLogService = new PerfectLogService();

        foreach ($collection as $key => $value) {
            if ($key == 0) continue;

            $row = $value->toArray();

            $saleId = str_replace('#', '', $row[0]);
            $productId = str_replace('#', '', $row[2]);

            $saleId = current(Hashids::connection('sale_id')->decode($saleId));
            $productId = current(Hashids::decode($productId));

            $sale = $saleModel->with(['plansSales.plan.productsPlans.product.productsPlanSales.tracking'])
                ->where('id', $saleId)
                ->where('owner_id', $this->user->account_owner_id)->first();

            if (isset($sale)) {

                $product = null;

                foreach ($sale->plansSales as $planSale) {
                    foreach ($planSale->plan->productsPlans as $productPlan) {
                        if($productId =  $productPlan->product->id){
                            $product = $productPlan->product;
                        }
                    }
                }

                if (isset($product)) {
                    $productPlanSale = $product->productsPlanSales->where('sale_id', $sale->id)
                        ->first();

                    $tracking = $productPlanSale->tracking;

                    if(isset($tracking) && isset($row[1])){
                        if($tracking->tracking_code != $row[1]){
                            $tracking->update([
                                'tracking_code' => $row[1],
                            ]);
                            $perfectLogService->track(Hashids::encode($tracking->id), $row[1]);
                        }
                    }else if(isset($row[1])) {
                        $tracking = $trackingService->createTracking($row[1], $productPlanSale);
                        $perfectLogService->track(Hashids::encode($tracking->id), $row[1]);
                    }
                }
            }
        }
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(){
                event(new TrackingsImportedEvent($this->user));
            },
        ];
    }
}
