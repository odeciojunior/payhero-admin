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
     * @param  User  $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param  Collection  $collection
     */
    public function collection(Collection $collection)
    {
        $saleModel = new Sale();
        $trackingService = new TrackingService();

        foreach ($collection as $key => $value) {
            if ($key == 0) {
                continue;
            }

            $row = $value->toArray();

            if (!empty($row[1]) && strlen($row[1]) <= 18) {
                $saleId = str_replace('#', '', $row[0]);
                $trackingCode = $row[1];
                $productId = str_replace('#', '', $row[2]);

                $saleId = current(Hashids::connection('sale_id')->decode($saleId));
                $productId = current(Hashids::decode($productId));

                $sale = $saleModel->with([
                    'productsPlansSale.tracking',
                    'productsPlansSale.sale.delivery'
                ])->where('id', $saleId)
                    ->where('owner_id', $this->user->account_owner_id)
                    ->first();

                if (!empty($sale)) {
                    $productPlanSale = $sale->productsPlansSale->where('product_id', $productId)->first();
                    if (!empty($productPlanSale)) {
                        $trackingService->createOrUpdateTracking($trackingCode, $productPlanSale);
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
