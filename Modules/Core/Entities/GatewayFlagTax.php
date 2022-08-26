<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property integer $gateway_id
 * @property integer $gateway_flag_id
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
    use LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "gateway_id",
        "gateway_flag_id",
        "installments",
        "type_enum",
        "percent",
        "active_flag",
        "created_at",
        "updated_at",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return BelongsTo
     */
    public function gatewayFlag()
    {
        return $this->belongsTo("Modules\Core\Entities\CardFlag");
    }
}
