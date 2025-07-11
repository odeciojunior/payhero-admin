<?php

namespace Modules\Trackings\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\User;
use Modules\Core\Events\TrackingsImportedEvent;
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
        $trackingService = new TrackingService();

        foreach ($collection as $key => $value) {
            if ($key == 0) {
                continue;
            }

            $row = $value->toArray();

            if (!empty($row[1]) && strlen($row[1]) <= 30 && strlen($row[1]) >= 9) {
                $saleId = str_replace("#", "", $row[0]);
                $trackingCode = $row[1];
                $productId = str_replace("#", "", $row[2]);

                $saleId = current(Hashids::connection("sale_id")->decode($saleId));
                $productId = current(Hashids::decode($productId));

                $pps = ProductPlanSale::select("products_plans_sales.id", "trackings.tracking_status_enum")
                    ->leftJoin("trackings", "trackings.product_plan_sale_id", "=", "products_plans_sales.id")
                    ->where("products_plans_sales.sale_id", $saleId)
                    ->where(function ($query) use ($productId) {
                        $query
                            ->where("products_plans_sales.product_id", $productId)
                            ->orWhere("products_sales_api_id", $productId);
                    })
                    ->first();

                // if (!empty($pps->tracking_status_enum) && $pps->tracking_status_enum == 3) {
                //     continue;
                // }

                if (!empty($pps) && !empty($trackingCode)) {
                    $trackingService->createOrUpdateTracking($trackingCode, $pps->id);
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
