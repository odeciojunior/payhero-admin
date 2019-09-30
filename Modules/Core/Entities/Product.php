<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property ProductPlanSale[] $productPlanSales
 * @property ProductPlan[] $productPlans
 */
class Product extends Model
{
    use SoftDeletes, FoxModelTrait;
    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * @var array
     */
    protected $appends = ['id_code'];
    /**
     * The "type" of the auto-incrementing ID.
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
        'shopify_variant_id',
        'project_id',
    ];

    /**
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function productPlanSales()
    {
        return $this->hasMany(ProductPlanSale::class);
    }

    /**
     * @return HasMany
     */
    public function productPlans()
    {
        return $this->hasMany(ProductPlan::class);
    }
}
