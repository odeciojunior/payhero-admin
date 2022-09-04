<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\PixelPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

/**
 * Modules\Core\Entities\Pixel
 *
 * @property int $id
 * @property int $project_id
 * @property string $name
 * @property string $code
 * @property string $platform
 * @property bool $status
 * @property string $checkout
 * @property string $send_value_checkout
 * @property string $purchase_all
 * @property string $basic_data
 * @property string $delivery
 * @property string $coupon
 * @property string $payment_info
 * @property string $purchase_card
 * @property string $purchase_boleto
 * @property string $purchase_pix
 * @property string $upsell
 * @property string $purchase_upsell
 * @property int $affiliate_id
 * @property Project $project
 * @property int|null $campaign_id
 * @property mixed|null $apply_on_plans
 * @property string|null $purchase_event_name
 * @property int $is_api
 * @property string|null $facebook_token
 * @property int $value_percentage_purchase_boleto
 * @property int $value_percentage_purchase_pix
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Affiliate|null $affiliate
 * @property-read string $id_code
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @method static Builder|Pixel newModelQuery()
 * @method static Builder|Pixel newQuery()
 * @method static \Illuminate\Database\Query\Builder|Pixel onlyTrashed()
 * @method static Builder|Pixel query()
 * @method static Builder|Pixel whereAffiliateId($value)
 * @method static Builder|Pixel whereApplyOnPlans($value)
 * @method static Builder|Pixel whereCampaignId($value)
 * @method static Builder|Pixel whereCheckout($value)
 * @method static Builder|Pixel whereCode($value)
 * @method static Builder|Pixel whereCodeMetaTagFacebook($value)
 * @method static Builder|Pixel whereCreatedAt($value)
 * @method static Builder|Pixel whereDeletedAt($value)
 * @method static Builder|Pixel whereFacebookToken($value)
 * @method static Builder|Pixel whereId($value)
 * @method static Builder|Pixel whereIsApi($value)
 * @method static Builder|Pixel whereName($value)
 * @method static Builder|Pixel wherePlatform($value)
 * @method static Builder|Pixel whereProjectId($value)
 * @method static Builder|Pixel wherePurchaseBoleto($value)
 * @method static Builder|Pixel wherePurchaseCard($value)
 * @method static Builder|Pixel wherePurchaseEventName($value)
 * @method static Builder|Pixel wherePurchasePix($value)
 * @method static Builder|Pixel whereStatus($value)
 * @method static Builder|Pixel whereUpdatedAt($value)
 * @method static Builder|Pixel whereValuePercentagePurchaseBoleto($value)
 * @method static \Illuminate\Database\Query\Builder|Pixel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Pixel withoutTrashed()
 * @mixin Eloquent
 */
class Pixel extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;

    public const STATUS_ACTIVE = 1;
    public const STATUS_DISABLED = 0;

    public const FACEBOOK_PLATFORM = "facebook";
    public const GOOGLE_ADWORDS_PLATFORM = "google_adwords";
    public const GOOGLE_ANALYTICS_PLATFORM = "google_analytics";
    public const GOOGLE_ANALYTICS_FOUR_PLATFORM = "google_analytics_four";
    public const TABOOLA_PLATFORM = "taboola";
    public const OUTBRAIN_PLATFORM = "outbrain";
    public const PINTEREST_PLATFORM = "pinterest";
    public const KWAI_PLATFORM = "kwai";

    /**
     * @var string
     */
    protected $presenter = PixelPresenter::class;
    /**
     * @var array
     */
    protected $fillable = [
        "project_id",
        "campaign_id",
        "name",
        "code",
        "platform",
        "status",
        "checkout",
        "send_value_checkout",
        "purchase_all",
        "basic_data",
        "delivery",
        "coupon",
        "payment_info",
        "purchase_card",
        "purchase_boleto",
        "purchase_pix",
        "upsell",
        "purchase_upsell",
        "affiliate_id",
        "apply_on_plans",
        "purchase_event_name",
        "is_api",
        "facebook_token",
        "url_facebook_domain",
        "value_percentage_purchase_boleto",
        "value_percentage_purchase_pix",
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }
}
