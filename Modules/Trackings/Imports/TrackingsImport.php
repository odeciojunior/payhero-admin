<?php

namespace Modules\Trackings\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Laracasts\Presenter\Exceptions\PresenterException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\User;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Events\TrackingsImportedEvent;
use Modules\Core\Services\ProductService;
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
     * @throws PresenterException
     */
    public function collection(Collection $collection)
    {
        $saleModel = new Sale();
        $trackingService = new TrackingService();
        $productService = new ProductService();

        foreach ($collection as $key => $value) {
            if ($key == 0) continue;

            $row = $value->toArray();

            if(isset($row[1]) && strlen($row[1]) <= 18) {

                $saleId = str_replace('#', '', $row[0]);
                $productId = str_replace('#', '', $row[2]);

                $saleId = current(Hashids::connection('sale_id')->decode($saleId));
                $productId = current(Hashids::decode($productId));

                $sale = $saleModel->with([
                    'productsPlansSale.tracking',
                    'productsPlansSale.sale.plansSales',
                    'productsPlansSale.sale.delivery'
                ])->where('id', $saleId)
                    ->where('owner_id', $this->user->account_owner_id)
                    ->first();

                if (isset($sale)) {
                    $productPlanSale = $sale->productsPlansSale->where('product_id', $productId)->first();
                    if (isset($productPlanSale)) {
                        if (isset($productPlanSale)) {
                            $tracking = $productPlanSale->tracking;
                            if (isset($tracking) && isset($row[1])) {
                                if ($tracking->tracking_code != $row[1]) {
                                    $apiTracking = $trackingService->sendTrackingToApi($tracking);
                                    if (!empty($apiTracking)) {
                                        $tracking->update([
                                            'tracking_code' => $row[1],
                                        ]);
                                    }
                                }
                            } else {
                                $tracking = new Tracking();
                                $tracking->tracking_code = $row[1];
                                $apiTracking = $trackingService->sendTrackingToApi($tracking);
                                if (!empty($apiTracking)) {
                                    $tracking = $trackingService->createTracking($row[1], $productPlanSale);
                                    $saleProducts = $productService->getProductsBySale($sale);
                                    event(new TrackingCodeUpdatedEvent($sale, $tracking, $saleProducts));
                                }
                            }
                        }
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
            AfterSheet::class => function () {
                event(new TrackingsImportedEvent($this->user));
            },
        ];
    }
}
