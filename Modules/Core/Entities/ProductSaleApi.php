<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use App\Traits\LogsActivity;

/**
 * Modules\Core\Entities\ProductSaleApi
 *
 * @property integer $id
 * @property int $sale_id
 * @property string $item_id
 * @property string $name
 * @property string $price
 * @property int $quantity
 * @property string $product_type
 * @property Sale $sale
 */

class ProductSaleApi extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products_sales_api';

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
        'sale_id',
        'item_id',
        'name',
        'price',
        'quantity',
        'product_type'
    ];

    /**
      * @return BelongsTo
      */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }  
}
