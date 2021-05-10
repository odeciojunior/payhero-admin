<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $checkout_id
 * @property string $created_at
 * @property string $updated_at
 * @property Checkout $checkout
 */
class SuspectBots extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    /**
     * @var array
     */
    protected $fillable = [
        'checkout_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function checkout()
    {
        return $this->belongsTo('Modules\Core\Entities\Checkout');
    }
}
