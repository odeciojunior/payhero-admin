<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Entities\Delivery;
use Modules\Core\Entities\PlanSale;

/**
 * @property integer $id
 * @property integer $product_plan_sale_id
 * @property integer $plans_sale_id
 * @property integer $delivery_id
 * @property string $tracking_code
 * @property boolean $tracking_type_enum
 * @property boolean $tracking_status_enum
 * @property string $tracking_date
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Delivery $delivery
 * @property PlanSale $planSale
 * @property ProductPlanSale $productPlanSale
 */
class Tracking extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'product_plan_sale_id',
        'plans_sale_id',
        'delivery_id',
        'tracking_date',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * @return BelongsTo
     */
    public function planSale()
    {
        return $this->belongsTo(PlanSale::class);
    }

    /**
     * @return BelongsTo
     */
    public function productPlanSale()
    {
        return $this->belongsTo(ProductPlanSale::class);
    }
}
