<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ProjectPresenter;
use Nwidart\Modules\Collection;
use Spatie\Activitylog\Models\Activity;

/**
 * @property int $id
 * @property int $carrier_id
 * @property string $photo
 * @property string $visibility
 * @property boolean $status
 * @property string $name
 * @property string $description
 * @property string $invoice_description
 * @property string $percentage_affiliates
 * @property string $terms_affiliates
 * @property string $url_page
 * @property boolean $automatic_affiliation
 * @property string $shopify_id
 * @property string $woocommerce_id
 * @property string $installments_amount
 * @property string $installments_interest_free
 * @property string $cookie_duration
 * @property boolean $url_cookies_checkout
 * @property string $contact
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $logo
 * @property boolean $boleto
 * @property string $boleto_redirect
 * @property string $card_redirect
 * @property string $pix_redirect
 * @property string $analyzing_redirect
 * @property string $support_phone
 * @property bool $countdown_timer_flag
 * @property string $countdown_timer_color
 * @property int $countdown_timer_time
 * @property string $countdown_timer_description
 * @property string $countdown_timer_finished_message
 * @property string $reviews_config_icon_type
 * @property string $reviews_config_icon_color
 * @property bool $product_amount_selector
 * @property json $notazz_configs
 * @property Collection $affiliateRequests
 * @property Collection $affiliates
 * @property Collection $checkouts
 * @property Collection $convertaxIntegrations
 * @property Collection $discountCoupons
 * @property Collection $domains
 * @property Collection $hotzappIntegrations
 * @property Collection $notazzIntegrations
 * @property Collection $pixels
 * @property Collection $plans
 * @property Collection $sales
 * @property Collection $shippings
 * @property Collection $shopifyIntegrations
 * @property Collection $usersProjects
 * @method ProjectPresenter present()
 */
class Project extends Model
{
    use FoxModelTrait, SoftDeletes, PresentableTrait, LogsActivity;
    public const STATUS_ACTIVE = 1;
    public const STATUS_DESABLE = 2;

    /**
     * @var bool
     */
    protected static $logFillable = true;
    /**
     * @var bool
     */
    protected static $logUnguarded = true;
    /**
     * Registra apenas os atributos alterados
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que o pacote armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;
    /**
     * @var string
     */
    protected $presenter = ProjectPresenter::class;
    /**
     * @var array
     */
    protected $appends = ['formatted_created_at', 'id_code'];
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'carrier_id',
        'photo',
        'visibility',
        'status',
        'name',
        'description',
        'invoice_description',
        'percentage_affiliates',
        'terms_affiliates',
        'status_url_affiliates',
        'commission_type_enum',
        'url_page',
        'automatic_affiliation',
        'shopify_id',
        'woocommerce_id',
        'installments_amount',
        'installments_interest_free',
        'cookie_duration',
        'url_cookies_checkout',
        'contact',
        'contact_verified',
        'logo',
        'boleto',
        'credit_card',
        'pix',
        'boleto_due_days',
        'boleto_redirect',
        'card_redirect',
        'pix_redirect',
        'analyzing_redirect',
        'support_phone',
        'support_phone_verified',
        'cost_currency_type',
        'discount_recovery_status',
        'discount_recovery_value',
        'checkout_type',
        'whatsapp_button',
        'credit_card_discount',
        'billet_discount',
        'pix_discount',
        'pre_selected_installment',
        'required_email_checkout',
        'document_type_checkout',
        'countdown_timer_flag',
        'countdown_timer_color',
        'countdown_timer_time',
        'countdown_timer_description',
        'countdown_timer_finished_message',
        'reviews_config_icon_type',
        'reviews_config_icon_color',
        'product_amount_selector',
        'finalizing_purchase_configs',
        'checkout_notification_configs',
        'notazz_configs',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'Projeto ' . $this->name . ' foi deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Projeto ' . $this->name . ' foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Projeto ' . $this->name . ' foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return HasMany
     */
    public function affiliateRequests()
    {
        return $this->hasMany(AffiliateRequest::class);
    }

    /**
     * @return HasMany
     */
    public function affiliates()
    {
        return $this->hasMany(Affiliate::class);
    }

    /**
     * @return HasMany
     */
    public function checkouts()
    {
        return $this->hasMany(Checkout::class);
    }

    /**
     * @return HasMany
     */
    public function convertaxIntegrations()
    {
        return $this->hasMany(ConvertaxIntegration::class);
    }

    /**
     * @return HasMany
     */
    public function discountCoupons()
    {
        return $this->hasMany(DiscountCoupon::class);
    }

    /**
     * @return HasMany
     */
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * @return HasMany
     */
    public function hotzappIntegrations()
    {
        return $this->hasMany(HotzappIntegration::class);
    }

    /**
     * @return HasMany
     */
    public function pixels()
    {
        return $this->hasMany(Pixel::class);
    }

    /**
     * @return HasMany
     */
    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * @return HasMany
     */
    public function shippings()
    {
        return $this->hasMany(Shipping::class);
    }

    /**
     * @return HasMany
     */
    public function shopifyIntegrations()
    {
        return $this->hasMany(ShopifyIntegration::class);
    }

    /**
     * @return HasMany
     */
    public function usersProjects()
    {
        return $this->hasMany(UserProject::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_projects', 'project_id', 'user_id');
    }

    /**
     * @return HasOne
     */
    public function notazzIntegration()
    {
        return $this->hasOne(NotazzIntegration::class);
    }

    /**
     * @return HasMany
     */
    public function notifications()
    {
        return $this->hasMany(ProjectNotification::class);
    }

    /**
     * @return HasMany
     */
    public function upsellRules()
    {
        return $this->hasMany(ProjectUpsellRule::class);
    }

    /**
     * @return HasOne
     */
    public function upsellConfig()
    {
        return $this->hasOne(ProjectUpsellConfig::class);
    }

    /**
     * @return HasMany
     */
    public function reviews()
    {
        return $this->hasMany(ProjectReviews::class);
    }

    /**
     * @return HasMany
     */
    public function orderBumpRules()
    {
        return $this->hasMany(OrderBumpRule::class);
    }

    public function getFinalizingPurchaseConfigToogleAttribute()
    {

        if (empty($this->finalizing_purchase_configs))
            return 0;

        $json_decode = json_decode($this->finalizing_purchase_configs, true);

        if (isset($json_decode['toogle']))
            return $json_decode['toogle'];

        return 0;
    }

    public function getFinalizingPurchaseConfigTextAttribute()
    {

        if (empty($this->finalizing_purchase_configs))
            return null;

        $json_decode = json_decode($this->finalizing_purchase_configs, true);

        if (isset($json_decode['text']))
            return $json_decode['text'];

        return null;
    }

    public function getFinalizingPurchaseConfigMinValueAttribute()
    {
        if (empty($this->finalizing_purchase_configs))
            return null;

        $json_decode = json_decode($this->finalizing_purchase_configs, true);

        if (isset($json_decode['min_value']))
            return $json_decode['min_value'];

        return null;
    }

    public function getCheckoutNotificationConfigsToogleAttribute()
    {

        if (empty($this->checkout_notification_configs))
            return 0;

        $json_decode = json_decode($this->checkout_notification_configs, true);

        if (isset($json_decode['toogle']))
            return $json_decode['toogle'];

        return 0;
    }

    public function getCheckoutNotificationConfigsTimeAttribute()
    {

        if (empty($this->checkout_notification_configs))
            return null;

        $json_decode = json_decode($this->checkout_notification_configs, true);

        if (isset($json_decode['time']))
            return $json_decode['time'];

        return null;
    }

    public function getCheckoutNotificationConfigsMobileAttribute()
    {
        if (empty($this->checkout_notification_configs))
            return null;

        $json_decode = json_decode($this->checkout_notification_configs, true);

        if (isset($json_decode['mobile']))
            return $json_decode['mobile'];

        return null;
    }

    public function getCheckoutNotificationConfigsMessageAttribute()
    {
        if (empty($this->checkout_notification_configs))
            return null;

        $json_decode = json_decode($this->checkout_notification_configs, true);

        if (isset($json_decode['messages'])) {
            $message_arr = $json_decode['messages'];
            $messages = array_map(
                function($message) {
                    $res = explode('//',$message);
                    return $res[0];
            },
                $message_arr
        );
            return $messages;
        }
        return null;
    }

    public function getCheckoutNotificationConfigsMessageMinValueAttribute()
    {
        if (empty($this->checkout_notification_configs))
            return null;

        $json_decode = json_decode($this->checkout_notification_configs, true);

        if (isset($json_decode['messages'])) {
            $message_arr = $json_decode['messages'];
            $messages = array_map(
                function($message) {
                    $res = explode('//',$message);
                    return $res[1];
                },
                $message_arr
            );
            return $messages;
        }
        return null;
    }
}
