<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $affiliate_id
 * @property integer $campaign_id
 * @property integer $plan_id
 * @property string $parameter
 * @property integer $clicks_amount
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Affiliate $affiliate
 * @property Campaign $campaign
 * @property Plan $plan
 */
class AffiliateLink extends Model
{

    use SoftDeletes;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = [
        'affiliate_id', 
        'campaign_id', 
        'plan_id', 
        'parameter', 
        'clicks_amount', 
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function affiliate()
    {
        return $this->belongsTo('Modules\Core\Entities\Affiliate');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo('Modules\Core\Entities\Campaign');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo('Modules\Core\Entities\Plan');
    }
}
