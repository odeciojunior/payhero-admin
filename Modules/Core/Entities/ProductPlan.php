<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $product_id
 * @property integer $plan_id
 * @property int $amount
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Plan $plan
 * @property Product $product
 */
class ProductPlan extends Model
{

    use SoftDeletes;

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
    protected $fillable = [
        'product_id', 
        'plan_id', 
        'amount', 
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo('Modules\Core\Entities\Plan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('Modules\Core\Entities\Product');
    }
}
