<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use App\Traits\LogsActivity;

/**
 * Modules\Core\Entities\Sale
 *
 * @property integer $id
 * @property int $sale_id
 * @property string $item_id
 * @property string $name
 * @property float $price
 * @property int $quantity
 * @property string $product_type
 * @mixin Eloquent
 */

class ProductSale extends Model
{
    use PresentableTrait, LogsActivity;

    protected $fillable = [
        'sale_id',
        'item_id',
        'name',
        'price',
        'quantity',
        'product_type'
    ];

    /**
     * @var bool
     */
    protected static $logFillable = true;
    /**
     * @var bool
     */
    protected static $logUnguarded = true;
    /**
     * Registra apenas os atributos alterados no log
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;
    
    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('Modules\Core\Entities\Sale');
    }    
}
