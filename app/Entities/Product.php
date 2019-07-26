<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $category
 * @property int $user
 * @property string $name
 * @property string $description
 * @property string $guarantee
 * @property boolean $format
 * @property string $cost
 * @property string $photo
 * @property string $height
 * @property string $width
 * @property string $weight
 * @property string $digital_product_url
 * @property boolean $shopify
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Category $category
 * @property User $user
 * @property ProductsPlan[] $productsPlans
 */
class Product extends Model
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'category',
        'user',
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
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Entities\Category', 'category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Entities\User', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productsPlans()
    {
        return $this->hasMany('App\Entities\ProductPlan', 'product');
    }

    public function plans()
    {
        return $this->belongsToMany('App\Entities\Plan', 'products_plans', 'product', 'plan');
    }
}
