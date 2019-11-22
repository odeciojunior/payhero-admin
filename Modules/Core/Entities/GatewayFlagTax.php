<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $gateway_id
 * @property integer $card_flag_id
 * @property int $installments
 * @property boolean $type_enum
 * @property float $percent
 * @property boolean $active_flag
 * @property string $created_at
 * @property string $updated_at
 * @property GatewayFlag $gatewayFlag
 */
class GatewayFlagTax extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = ['gateway_id', 'card_flag_id', 'installments', 'type_enum', 'percent', 'active_flag', 'created_at', 'updated_at'];

    /**
     * @return BelongsTo
     */
    public function gatewayFlag()
    {
        return $this->belongsTo('Modules\Core\Entities\CardFlag');
    }
}
