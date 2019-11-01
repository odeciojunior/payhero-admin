<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TrackingPresenter;

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
    use PresentableTrait;
    protected $presenter = TrackingPresenter::class;

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
        'sale_id',
        'product_id',
        'amount',
        'delivery_id',
        'tracking_code',
        'tracking_status_enum',
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
    public function productPlanSale()
    {
        return $this->belongsTo(ProductPlanSale::class);
    }

    public function sale(){
        return $this->belongsTo(Sale::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function history()
    {
        return $this->hasMany(TrackingHistory::class);
    }
}
