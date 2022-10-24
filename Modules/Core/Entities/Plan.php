<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\PlanPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

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
 * @property PlanSale[] $planSales
 * @property ProductPlan[] $productPlans
 * @property SmsMessage[] $smsMessages
 * @property ZenviaSm[] $zenviaSms
 */
class Plan extends Model
{
    use SoftDeletes, PresentableTrait, FoxModelTrait, LogsActivity;

    public const STATUS_ACTIVE = 1;
    public const STATUS_DESABLE = 0;

    public const ACTIVE_FLAG_ACTIVE = 1;
    public const ACTIVE_FLAG_DESABLE = 0;

    /**
     * @var array
     */
    protected $appends = ["id_code"];
    /**
     * @var string
     */
    protected $presenter = PlanPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "project_id",
        "name",
        "description",
        "code",
        "price",
        "status",
        "shopify_id",
        "shopify_variant_id",
        "active_flag",
        "processing_cost",
        "created_at",
        "updated_at",
        "deleted_at",
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
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return HasMany
     */
    public function affiliateLinks()
    {
        return $this->hasMany(AffiliateLink::class);
    }

    /**
     * @return HasMany
     */
    public function checkoutPlans()
    {
        return $this->hasMany(CheckoutPlan::class);
    }

    /**
     * @return HasMany
     */
    public function planGifts()
    {
        return $this->hasMany(PlanGift::class);
    }

    /**
     * @return HasMany
     */
    public function plansSales()
    {
        return $this->hasMany(PlanSale::class);
    }

    /**
     * @return HasMany
     */
    public function productsPlans()
    {
        return $this->hasMany(ProductPlan::class);
    }

    /**
     * @return HasMany
     */
    public function smsMessages()
    {
        return $this->hasMany("Modules\Core\Entities\SmsMessage", "plan");
    }

    /**
     * @return HasMany
     */
    public function zenviaSms()
    {
        return $this->hasMany("Modules\Core\Entities\ZenviaSms");
    }

    /**
     * @return BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, "products_plans", "plan_id", "product_id");
    }

    public function variants()
    {
        return $this->hasMany(Product::class, 'shopify_id', 'shopify_id')
            ->orderBy('description');
    }
}
