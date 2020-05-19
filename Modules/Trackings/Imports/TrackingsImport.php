<?php

namespace Modules\Trackings\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
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
     * @param  User  $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param  Collection  $collection
     * @throws PresenterException
     */
    public function collection(Collection $collection)
    {
        $saleModel = new Sale();
        $trackingService = new TrackingService();
        $trackingModel = new Tracking();
        $productService = new ProductService();

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
                        $tracking = $productPlanSale->tracking;

                        //verifica se já tem uma venda nessa conta com o mesmo código de rastreio
                        $exists = $trackingModel->where('trackings.tracking_code', $trackingCode)
                            ->where('sale_id', '!=', $sale->id)
                            ->whereHas('sale', function ($query) use ($sale) {
                                $query->where('owner_id', $sale->owner_id);
                            })->exists();
                        if ($exists) {
                            continue;
                        }

                        $apiTracking = $trackingService->sendTrackingToApi($trackingCode);

                        if (!empty($apiTracking)) {
                            //verifica se a data de postagem na transportadora é menor que a data da venda
                            if (!empty($apiTracking->origin_info)) {
                                $postDate = Carbon::parse($apiTracking->origin_info->ItemReceived);
                                if ($postDate->lt($productPlanSale->created_at)) {
                                    if (!$apiTracking->already_exists) { // deleta na api caso seja recém criado
                                        $trackingService->deleteTrackingApi($apiTracking);
                                    }
                                    continue;
                                }
                            }

                            $statusEnum = $trackingService->parseStatusApi($apiTracking->status);

                            if (!empty($tracking)) {
                                if ($tracking->tracking_code != $trackingCode) {
                                    $tracking->update([
                                        'tracking_code' => $trackingCode,
                                        'tracking_status_enum' => $statusEnum,
                                    ]);
                                    $saleProducts = $productService->getProductsBySale($sale);
                                    event(new TrackingCodeUpdatedEvent($sale, $tracking, $saleProducts));
                                }
                            } else {
                                $tracking = $trackingService->createTracking($trackingCode, $productPlanSale,
                                    $statusEnum);
                                $saleProducts = $productService->getProductsBySale($sale);
                                event(new TrackingCodeUpdatedEvent($sale, $tracking, $saleProducts));
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
