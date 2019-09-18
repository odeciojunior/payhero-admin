<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'gift_id', 
        'plan_id', 
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gift()
    {
        return $this->belongsTo('Modules\Core\Entities\Gift');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo('Modules\Core\Entities\Plan');
    }
}
