<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = [
        'delivery_id',
        'plans_sale_id',
        'tracking_code',
        'created_at',
        'updated_at',
        'tracking_type_enum',
        'tracking_status_enum',
        'tracking_date',
        'description',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}
