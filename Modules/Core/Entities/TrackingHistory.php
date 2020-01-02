<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $delivery_id
 * @property integer $plans_sale_id
 * @property string $tracking_code
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $tracking_type_enum
 * @property boolean $tracking_status_enum
 * @property string $tracking_date
 * @property string $description
 * @property string $deleted_at
 * @property Delivery $delivery
 */
class TrackingHistory extends Model
{
    use LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'tracking_id',
        'created_at',
        'updated_at',
        'tracking_status_enum',
        'deleted_at',
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
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}
