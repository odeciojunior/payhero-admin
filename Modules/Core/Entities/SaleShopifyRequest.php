<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SaleShopifyRequest
 * @package Modules\Core\Entities
 * @property integer $id
 * @property string $project
 * @property string $method
 * @property integer $sale_id
 * @property json $send_data
 * @property json $received_data
 * @property json $exceptions
 */
class SaleShopifyRequest extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        "project",
        "method",
        "sale_id",
        "send_data",
        "received_data",
        "exceptions",
    ];

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
