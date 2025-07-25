<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ProductPlanSalePresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $product_id
 * @property integer $plan_id
 * @property integer $sale_id
 * @property string $name
 * @property string $description
 * @property string $guarantee
 * @property string $format
 * @property string $cost
 * @property string $photo
 * @property string $height
 * @property string $width
 * @property string $weight
 * @property string $shopify
 * @property string $digital_product_url
 * @property string $price
 * @property string $shopify_id
 * @property string $shopify_variant
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Plan $plan
 * @property Product $product
 * @property Sale $sale
 * @property Tracking[] $trackings
 */
class ProductPlanSale extends Model
{
    use PresentableTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = ProductPlanSalePresenter::class;
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "products_plans_sales";
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "product_id",
        "plan_id",
        "products_sales_api_id",
        "sale_id",
        "amount",
        "name",
        "description",
        "guarantee",
        "format",
        "cost",
        "photo",
        "height",
        "width",
        "weight",
        "shopify",
        "digital_product_url",
        "price",
        "shopify_id",
        "shopify_variant_id",
        "temporary_url",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo("Modules\Core\Entities\Plan");
    }

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo("Modules\Core\Entities\Product");
    }

    /**
     * @return BelongsTo
     */
    public function productSaleApi()
    {
        return $this->belongsTo(ProductSaleApi::class, "products_sales_api_id");
    }

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo("Modules\Core\Entities\Sale");
    }

    /**
     * @return HasOne
     */
    public function tracking()
    {
        return $this->hasOne("Modules\Core\Entities\Tracking")->latest();
    }
}
