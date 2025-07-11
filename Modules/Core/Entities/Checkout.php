<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\CheckoutPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property integer $id
 * @property int $project_id
 * @property int $affiliate_id
 * @property string $created_at
 * @property string $status
 * @property int $status_enum
 * @property string $operational_system
 * @property string $browser
 * @property string $id_log_session
 * @property string $ip
 * @property string $ip_localization
 * @property string $ip_state
 * @property string $city
 * @property string $state
 * @property string $state_name
 * @property string $zip_code
 * @property string $country
 * @property string $parameter
 * @property string $currency
 * @property string $lat
 * @property string $lon
 * @property string $src
 * @property boolean $is_mobile
 * @property string $referer
 * @property string $original_url
 * @property string $pixel_data
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $utm_source
 * @property string $utm_medium
 * @property string $utm_campaign
 * @property string $utm_term
 * @property string $utm_content
 * @property int $email_sent_amount
 * @property int $sms_sent_amount
 * @property Project $project
 * @property Affiliate $affiliate
 * @property CheckoutPlan[] $checkoutPlans
 * @property Log[] $logs
 * @property Sale[] $sales
 */
class Checkout extends Model
{
    use FoxModelTrait, SoftDeletes, PresentableTrait, LogsActivity;
    use HasFactory;

    const STATUS_ACCESSED = 1;
    const STATUS_ABANDONED_CART = 2;
    const STATUS_RECOVERED = 3;
    const STATUS_SALE_FINALIZED = 4;

    const OPERATIONAL_SYSTEM_UNKNOWN = 0;
    const OPERATIONAL_SYSTEM_IOS = 1;
    const OPERATIONAL_SYSTEM_ANDROID = 2;
    const OPERATIONAL_SYSTEM_WINDOWS = 3;
    const OPERATIONAL_SYSTEM_LINUX = 4;
    const OPERATIONAL_SYSTEM_CHROME = 5;
    const OPERATIONAL_SYSTEM_BLACK_BERRY = 6;
    const OPERATIONAL_SYSTEM_JAVA = 7;

    public const CHECKOUT_ONE_STEP = 2;

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var string
     */
    protected $presenter = CheckoutPresenter::class;
    /**
     * @var array
     */
    protected $fillable = [
        "project_id",
        "affiliate_id",
        "status",
        "status_enum",
        "operational_system",
        "os_enum",
        "browser",
        "id_log_session",
        "ip",
        "ip_localization",
        "ip_state",
        "city",
        "state",
        "state_name",
        "zip_code",
        "country",
        "parameter",
        "currency",
        "lat",
        "lon",
        "src",
        "is_mobile",
        "utm_source",
        "utm_medium",
        "utm_campaign",
        "utm_term",
        "utm_content",
        "email_sent_amount",
        "sms_sent_amount",
        "client_name",
        "referer",
        "original_url",
        "pixel_data",
        "client_telephone",
        "template_type",
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
        return $this->belongsTo("Modules\Core\Entities\Project");
    }

    /**
     * @return BelongsTo
     */
    public function affiliate()
    {
        return $this->belongsTo("Modules\Core\Entities\Affiliate");
    }

    /**
     * @return HasMany
     */
    public function checkoutPlans()
    {
        return $this->hasMany("Modules\Core\Entities\CheckoutPlan");
    }

    /**
     * @return HasMany
     */
    public function logs()
    {
        return $this->hasMany("Modules\Core\Entities\Log");
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany("Modules\Core\Entities\Sale");
    }
}
