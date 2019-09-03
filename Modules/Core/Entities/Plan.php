<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property int $project_id
 * @property string $name
 * @property string $description
 * @property string $code
 * @property float $price
 * @property boolean $status
 * @property string $shopify_id
 * @property string $shopify_variant_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property AffiliateLink[] $affiliateLinks
 * @property CheckoutPlan[] $checkoutPlans
 * @property PlanGift[] $planGifts
 * @property PlansSale[] $plansSales
 * @property ProductsPlan[] $productsPlans
 * @property SmsMessage[] $smsMessages
 * @property ZenviaSm[] $zenviaSms
 */
class Plan extends Model
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
        'project_id', 
        'name', 
        'description', 
        'code', 
        'price', 
        'status', 
        'shopify_id', 
        'shopify_variant_id', 
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Entities\Project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliateLinks()
    {
        return $this->hasMany('App\Entities\AffiliateLink');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkoutPlans()
    {
        return $this->hasMany('App\Entities\CheckoutPlan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planGifts()
    {
        return $this->hasMany('App\Entities\PlanGift');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plansSales()
    {
        return $this->hasMany('App\Entities\PlansSale');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productsPlans()
    {
        return $this->hasMany('App\Entities\ProductsPlan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function smsMessages()
    {
        return $this->hasMany('App\Entities\SmsMessage', 'plan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zenviaSms()
    {
        return $this->hasMany('App\Entities\ZenviaSm');
    }
}
