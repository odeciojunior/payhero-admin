<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use FoxModelTrait, SoftDeletes;
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
        'url_page',
        'automatic_affiliation',
        'shopify_id',
        'installments_amount',
        'installments_interest_free',
        'cookie_duration',
        'url_cookies_checkout',
        'contact',
        'logo',
        'boleto',
        'boleto_redirect',
        'card_redirect',
        'analyzing_redirect',
        'support_phone',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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
     * @return HasMany
     */
    public function zenviaSms()
    {
        return $this->hasMany('Modules\Core\Entities\ZenviaSm');
    }

    /**
     * @return HasOne
     */
    public function notazzIntegration()
    {
        return $this->hasOne('Modules\Core\Entities\NotazzIntegration');
    }
}
