<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property integer $checkout_id
 * @property integer $plan_id
 * @property string $amount
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Checkout $checkout
 * @property Plan $plan
 */
class CheckoutPlan extends Model
{
    use SoftDeletes, LogsActivity, HasFactory;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = ["checkout_id", "plan_id", "amount", "created_at", "updated_at", "deleted_at"];

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
    public function checkout()
    {
        return $this->belongsTo("Modules\Core\Entities\Checkout");
    }

    /**
     * @return BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo("Modules\Core\Entities\Plan");
    }
}
