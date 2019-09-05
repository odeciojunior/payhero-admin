<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $category_id
 * @property int $user_id
 * @property string $name
 * @property string $description
 * @property string $guarantee
 * @property boolean $format
 * @property string $cost
 * @property string $photo
 * @property string $height
 * @property string $width
 * @property string $weight
 * @property boolean $shopify
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $digital_product_url
 * @property string $price
 * @property string $shopify_id
 * @property string $shopify_variant_id
 * @property Category $category
 * @property User $user
 * @property PlanSaleProduct[] $planSaleProducts
 * @property ProductsPlan[] $productsPlans
 */
class Product extends Model
{

    use SoftDeletes;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = [
        'category_id', 
        'user_id', 
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
        'created_at', 
        'updated_at', 
        'deleted_at', 
        'digital_product_url', 
        'price', 
        'shopify_id', 
        'shopify_variant_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('Modules\Core\Entities\Category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planSaleProducts()
    {
        return $this->hasMany('Modules\Core\Entities\PlanSaleProduct');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productsPlans()
    {
        return $this->hasMany('Modules\Core\Entities\ProductPlan');
    }
}
