<?php

namespace Modules\Core\Events;

use Illuminate\Support\Collection;
use Modules\Core\Entities\ProductPlanSale;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\Sale;

class TrackingCodeUpdatedEvent
{
    use SerializesModels;
    public $sale;
    public $productPlanSale;
    public $products;

    /**
     * TrackingCodeUpdatedEvent constructor.
     * @param Sale $sale
     * @param ProductPlanSale $productPlanSale
     * @param Collection $products
     */
    public function __construct(Sale $sale, ProductPlanSale $productPlanSale, Collection $products)
    {
        $this->sale = $sale;
        $this->productPlanSale = $productPlanSale;
        $this->products = $products;
    }

    /**
     * Get the channels the event should be broadcast on.
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
