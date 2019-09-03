<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

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
    /**
     * @var array
     */
    protected $fillable = ['carrier_id', 'photo', 'visibility', 'status', 'name', 'description', 'invoice_description', 'percentage_affiliates', 'url_page', 'automatic_affiliation', 'shopify_id', 'installments_amount', 'installments_interest_free', 'cookie_duration', 'url_cookies_checkout', 'contact', 'created_at', 'updated_at', 'deleted_at', 'logo', 'boleto', 'boleto_redirect', 'card_redirect', 'analyzing_redirect', 'support_phone'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier()
    {
        return $this->belongsTo('App\Entities\Carrier');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliateRequests()
    {
        return $this->hasMany('App\Entities\AffiliateRequest');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliates()
    {
        return $this->hasMany('App\Entities\Affiliate');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkouts()
    {
        return $this->hasMany('App\Entities\Checkout');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientsCookies()
    {
        return $this->hasMany('App\Entities\ClientsCookie');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function convertaxIntegrations()
    {
        return $this->hasMany('App\Entities\ConvertaxIntegration');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function discountCoupons()
    {
        return $this->hasMany('App\Entities\DiscountCoupon');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domains()
    {
        return $this->hasMany('App\Entities\Domain');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function extraMaterials()
    {
        return $this->hasMany('App\Entities\ExtraMaterial');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gifts()
    {
        return $this->hasMany('App\Entities\Gift', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hotzappIntegrations()
    {
        return $this->hasMany('App\Entities\HotzappIntegration');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function layouts()
    {
        return $this->hasMany('App\Entities\Layout', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notazzIntegrations()
    {
        return $this->hasMany('App\Entities\NotazzIntegration');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pixels()
    {
        return $this->hasMany('App\Entities\Pixel');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plans()
    {
        return $this->hasMany('App\Entities\Plan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany('App\Entities\Sale');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippings()
    {
        return $this->hasMany('App\Entities\Shipping', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopifyIntegrations()
    {
        return $this->hasMany('App\Entities\ShopifyIntegration');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersProjects()
    {
        return $this->hasMany('App\Entities\UsersProject');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zenviaSms()
    {
        return $this->hasMany('App\Entities\ZenviaSm');
    }
}
