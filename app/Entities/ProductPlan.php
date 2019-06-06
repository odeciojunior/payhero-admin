<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $product
 * @property integer $plan
 * @property int $amount
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Plan $plan
 * @property Product $product
 */
class ProductPlan extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'products_plans';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['product', 'plan', 'amount', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo('App\Entities\Plan', 'plan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Entities\Product', 'product');
    }
}
