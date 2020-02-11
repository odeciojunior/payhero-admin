<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ProjectPresenter;
use App\Traits\LogsActivity;
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
 * @property string $analyzing_redirect
 * @property string $support_phone
 * @property Carrier $carrier
 * @property AffiliateRequest[] $affiliateRequests
 * @property Affiliate[] $affiliates
 * @property Checkout[] $checkouts
 * @property ClientsCookie[] $clientsCookies
 * @property ConvertaxIntegration[] $convertaxIntegrations
 * @property DiscountCoupon[] $discountCoupons
 * @property Domain[] $domains
 * @property ExtraMaterial[] $extraMaterials
 * @property Gift[] $gifts
 * @property HotzappIntegration[] $hotzappIntegrations
 * @property Layout[] $layouts
 * @property NotazzIntegration[] $notazzIntegrations
 * @property Pixel[] $pixels
 * @property Plan[] $plans
 * @property Sale[] $sales
 * @property Shipping[] $shippings
 * @property ShopifyIntegration[] $shopifyIntegrations
 * @property UsersProject[] $usersProjects
 * @property ZenviaSm[] $zenviaSms
 */
class Project extends Model
{
    use FoxModelTrait, SoftDeletes, PresentableTrait, LogsActivity;
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
        'installments_amount',
        'installments_interest_free',
        'cookie_duration',
        'url_cookies_checkout',
        'contact',
        'contact_verified',
        'logo',
        'boleto',
        'credit_card',
        'boleto_due_days',
        'boleto_redirect',
        'card_redirect',
        'analyzing_redirect',
        'support_phone',
        'support_phone_verified',
        'cost_currency_type',
        'discount_recovery_status',
        'discount_recovery_value',
        'checkout_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
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
     * @return mixed
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    /**
     * @return BelongsTo
     */
    public function carrier()
    {
        return $this->belongsTo('Modules\Core\Entities\Carrier');
    }

    /**
     * @return HasMany
     */
    public function affiliateRequests()
    {
        return $this->hasMany('Modules\Core\Entities\AffiliateRequest');
    }

    /**
     * @return HasMany
     */
    public function affiliates()
    {
        return $this->hasMany('Modules\Core\Entities\Affiliate');
    }

    /**
     * @return HasMany
     */
    public function checkouts()
    {
        return $this->hasMany('Modules\Core\Entities\Checkout');
    }

    /**
     * @return HasMany
     */
    public function clientsCookies()
    {
        return $this->hasMany('Modules\Core\Entities\ClientsCookie');
    }

    /**
     * @return HasMany
     */
    public function convertaxIntegrations()
    {
        return $this->hasMany('Modules\Core\Entities\ConvertaxIntegration');
    }

    /**
     * @return HasMany
     */
    public function discountCoupons()
    {
        return $this->hasMany('Modules\Core\Entities\DiscountCoupon');
    }

    /**
     * @return HasMany
     */
    public function domains()
    {
        return $this->hasMany('Modules\Core\Entities\Domain');
    }

    /**
     * @return HasMany
     */
    public function extraMaterials()
    {
        return $this->hasMany('Modules\Core\Entities\ExtraMaterial');
    }

    /**
     * @return HasMany
     */
    public function gifts()
    {
        return $this->hasMany('Modules\Core\Entities\Gift');
    }

    /**
     * @return HasMany
     */
    public function hotzappIntegrations()
    {
        return $this->hasMany('Modules\Core\Entities\HotzappIntegration');
    }

    /**
     * @return HasMany
     */
    public function layouts()
    {
        return $this->hasMany('Modules\Core\Entities\Layout', 'project');
    }

    /**
     * @return HasMany
     */
    public function pixels()
    {
        return $this->hasMany('Modules\Core\Entities\Pixel');
    }

    /**
     * @return HasMany
     */
    public function plans()
    {
        return $this->hasMany('Modules\Core\Entities\Plan');
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany('Modules\Core\Entities\Sale');
    }

    /**
     * @return HasMany
     */
    public function shippings()
    {
        return $this->hasMany('Modules\Core\Entities\Shipping');
    }

    /**
     * @return HasMany
     */
    public function shopifyIntegrations()
    {
        return $this->hasMany('Modules\Core\Entities\ShopifyIntegration');
    }

    /**
     * @return HasMany
     */
    public function usersProjects()
    {
        return $this->hasMany('Modules\Core\Entities\UserProject');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('Modules\Core\Entities\User', 'users_projects', 'project_id', 'user_id');
    }

    /**
     * @return HasOne
     */
    public function notazzIntegration()
    {
        return $this->hasOne('Modules\Core\Entities\NotazzIntegration');
    }


    /**
     * @return HasMany
     */
    public function notifications()
    {
        return $this->hasMany('Modules\Core\Entities\ProjectNotification');
    }

}
