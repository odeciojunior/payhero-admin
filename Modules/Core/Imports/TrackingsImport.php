<?php

namespace Modules\Core\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\Core\Entities\Sale;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsImport implements ToCollection, WithChunkReading, ShouldQueue
{
    /**
     * @var int
     */
    private $userId;

    /**
     * TrackingsImport constructor.
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $saleModel = new Sale();

        foreach ($collection as $key => $value) {
            if ($key == 0) continue;

            $row = $value->toArray();

            $saleId = str_replace('#', '', $row[0]);
            $productId = str_replace('#', '', $row[2]);

            $saleId = current(Hashids::connection('sale_id')->decode($saleId));
            $productId = current(Hashids::decode($productId));

            $sale = $saleModel->with(['plansSales.plan.productsPlans.product.productsPlanSales.tracking'])
                ->where('id', $saleId)
                ->where('owner_id', $this->userId)->first();

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

                    if(isset($tracking) && isset($row[5]) && $tracking->tracking_code != $row[5]){
                        //$tracking->update([
                        //    'tracking_code' => $row[5],
                        //]);
                    }else{
                        //$tracking = $trackingService->createTracking($row[0], $productPlanSale);
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
}
