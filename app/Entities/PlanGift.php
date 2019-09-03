<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

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
    /**
     * @var array
     */
    protected $fillable = ['gift_id', 'plan_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gift()
    {
        return $this->belongsTo('App\Entities\Gift');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo('App\Entities\Plan');
    }
}
