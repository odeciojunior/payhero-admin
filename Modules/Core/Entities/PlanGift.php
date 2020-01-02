<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $gift_id
 * @property integer $plan_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Gift $gift
 * @property Plan $plan
 */
class PlanGift extends Model
{
    use SoftDeletes, LogsActivity;
    /**
     * @var array
     */
    protected $fillable = [
        'gift_id',
        'plan_id',
        'created_at',
        'updated_at',
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
    public function gift()
    {
        return $this->belongsTo('Modules\Core\Entities\Gift');
    }

    /**
     * @return BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo('Modules\Core\Entities\Plan');
    }
}
