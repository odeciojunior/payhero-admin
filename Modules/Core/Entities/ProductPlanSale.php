<?php

namespace ModulesCoreEntities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;

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
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'products_plans_sales';

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'product_id',
        'plan_id',
        'sale_id',
        'name',
        'description',
        'guarantee',
        'format',
        'cost',
        'photo',
        'height',
        'width',
        'weight',
        'shopify',
        'digital_product_url',
        'price',
        'shopify_id',
        'shopify_variant',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo('ModulesCoreEntities\Plan');
    }

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('ModulesCoreEntities\Product');
    }

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('ModulesCoreEntities\Sale');
    }

    /**
     * @return HasMany
     */
    public function trackings()
    {
        return $this->hasMany('ModulesCoreEntities\Tracking');
    }
}
