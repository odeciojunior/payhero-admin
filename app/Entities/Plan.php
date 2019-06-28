<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property int $company
 * @property int $project
 * @property int $layout
 * @property int $hotzapp_integration
 * @property int $carrier
 * @property string $name
 * @property string $description
 * @property int $amount
 * @property string $code
 * @property float $price
 * @property boolean $status
 * @property string $id_plan_carrier
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $photo
 * @property string $shopify_id
 * @property string $shopify_variant_id
 * @property Company $company
 * @property HotzappIntegration $hotzappIntegration
 * @property Project $project
 * @property Carrier $carrier
 * @property Layout $layout
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
    use FoxModelTrait;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'company',
        'project',
        'layout',
        'hotzapp_integration',
        'carrier',
        'name',
        'description',
        'amount',
        'code',
        'price',
        'status',
        'id_plan_carrier',
        'created_at',
        'updated_at',
        'deleted_at',
        'photo',
        'shopify_id',
        'shopify_variant_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Entities\Company', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotzappIntegration()
    {
        return $this->belongsTo('App\Entities\HotzappIntegration', 'hotzapp_integration');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Entities\Project', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier()
    {
        return $this->belongsTo('App\Entities\Carrier', 'carrier');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function layout()
    {
        return $this->belongsTo('App\Entities\Layout', 'layout');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliateLinks()
    {
        return $this->hasMany('App\Entities\AffiliateLink', 'plan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkoutPlans()
    {
        return $this->hasMany('App\Entities\CheckoutPlan', 'plan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planGifts()
    {
        return $this->hasMany('App\Entities\PlanGift', 'plan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plansSales()
    {
        return $this->hasMany('App\Entities\PlansSale', 'plan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productsPlans()
    {
        return $this->hasMany('App\Entities\ProductPlan', 'plan');
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
        return $this->hasMany('App\Entities\ZenviaSm', 'plan');
    }
}
