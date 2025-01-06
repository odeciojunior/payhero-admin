<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ProjectPresenter;
use Spatie\Activitylog\LogOptions;

/**
 * @property int $id
 * @property integer $carrier_id
 * @property string $photo
 * @property string $visibility
 * @property boolean $status
 * @property string $name
 * @property string $description
 * @property string $percentage_affiliates
 * @property string $terms_affiliates
 * @property string $status_url_affiliates
 * @property string $commission_type_enum
 * @property string $url_page
 * @property boolean $automatic_affiliation
 * @property string $shopify_id
 * @property string $woocommerce_id
 * @property string $nuvemshop_id
 * @property string $cookie_duration
 * @property boolean $url_cookies_checkout
 * @property string $boleto_redirect
 * @property string $card_redirect
 * @property string $pix_redirect
 * @property string $analyzing_redirect
 * @property string $cost_currency_type
 * @property string $discount_recovery_status
 * @property string $discount_recovery_value
 * @property string $reviews_config_icon_type
 * @property string $reviews_config_icon_color
 * @property string $notazz_configs
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Collection $affiliateRequests
 * @property Collection $affiliates
 * @property Collection $checkouts
 * @property Collection $convertaxIntegrations
 * @property Collection $discountCoupons
 * @property Collection $domains
 * @property Collection $hotzappIntegrations
 * @property Collection $pixels
 * @property Collection $plans
 * @property Collection $sales
 * @property Collection $shippings
 * @property Collection $shopifyIntegrations
 * @property Collection $woocommerceIntegrations
 * @property Collection $usersProjects
 * @property Collection $users
 * @property NotazzIntegration $notazzIntegration
 * @property Collection $notifications
 * @property Collection $upsellRules
 * @property ProjectUpsellConfig $upsellConfig
 * @property Collection $reviews
 * @property Collection $orderBumpRules
 * @property PixelConfig $pixelConfigs
 * @property CheckoutConfig $checkoutConfig
 * @property ApiToken $apiToken
 * @method ProjectPresenter present()
 */
class Project extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;
    use HasFactory;

    public const STATUS_ACTIVE = 1;
    public const STATUS_DESABLE = 2;

    public const DEMO_ID = 1;

    protected $presenter = ProjectPresenter::class;

    protected $appends = ["formatted_created_at", "id_code"];

    protected $dates = ["deleted_at"];

    protected $fillable = [
        "carrier_id",
        "photo",
        "visibility",
        "status",
        "name",
        "description",
        "percentage_affiliates",
        "terms_affiliates",
        "status_url_affiliates",
        "commission_type_enum",
        "url_page",
        "automatic_affiliation",
        "shopify_id",
        "woocommerce_id",
        "nuvemshop_id",
        "cookie_duration",
        "url_cookies_checkout",
        "boleto_redirect",
        "card_redirect",
        "pix_redirect",
        "analyzing_redirect",
        "cost_currency_type",
        "discount_recovery_status",
        "discount_recovery_value",
        "reviews_config_icon_type",
        "reviews_config_icon_color",
        "notazz_configs",
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

    public function affiliateRequests(): HasMany
    {
        return $this->hasMany(AffiliateRequest::class);
    }

    public function affiliates(): HasMany
    {
        return $this->hasMany(Affiliate::class);
    }

    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class);
    }

    public function convertaxIntegrations(): HasMany
    {
        return $this->hasMany(ConvertaxIntegration::class);
    }

    public function discountCoupons(): HasMany
    {
        return $this->hasMany(DiscountCoupon::class);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function hotzappIntegrations(): HasMany
    {
        return $this->hasMany(HotzappIntegration::class);
    }

    public function pixels(): HasMany
    {
        return $this->hasMany(Pixel::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function shippings(): HasMany
    {
        return $this->hasMany(Shipping::class);
    }

    public function shopifyIntegrations(): HasMany
    {
        return $this->hasMany(ShopifyIntegration::class);
    }

    public function woocommerceIntegrations(): HasMany
    {
        return $this->hasMany(WooCommerceIntegration::class);
    }

    public function usersProjects(): HasMany
    {
        return $this->hasMany(UserProject::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "users_projects", "project_id", "user_id");
    }

    public function notazzIntegration(): HasOne
    {
        return $this->hasOne(NotazzIntegration::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ProjectNotification::class);
    }

    public function upsellRules(): HasMany
    {
        return $this->hasMany(ProjectUpsellRule::class);
    }

    public function upsellConfig(): HasOne
    {
        return $this->hasOne(ProjectUpsellConfig::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProjectReviews::class);
    }

    public function orderBumpRules(): HasMany
    {
        return $this->hasMany(OrderBumpRule::class);
    }

    public function pixelConfigs(): HasOne
    {
        return $this->hasOne(PixelConfig::class);
    }

    public function checkoutConfig(): HasOne
    {
        return $this->hasOne(CheckoutConfig::class);
    }

    public function apiToken(): BelongsTo
    {
        return $this->belongsTo(ApiToken::class, 'id', 'project_id');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at
            ? $this->created_at->format('d/m/Y H:i')
            : null;
    }
}
