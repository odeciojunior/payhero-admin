<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $carrier
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
 * @property string $url_redirect
 * @property boolean $boleto
 * @property Carrier $carrier
 * @property AffiliateRequest[] $affiliateRequests
 * @property Affiliate[] $affiliates
 * @property Checkout[] $checkouts
 * @property ClientsCookie[] $clientsCookies
 * @property DiscountCoupon[] $discountCoupons
 * @property Domain[] $domains
 * @property ExtraMaterial[] $extraMaterials
 * @property Gift[] $gifts
 * @property Layout[] $layouts
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
    use FoxModelTrait;
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'carrier',
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
        'created_at',
        'updated_at',
        'logo',
        'boleto_redirect',
        'card_redirect',
        'analyzing_redirect',
        'boleto',
        'deleted_at',
    ];
    /**
     * @var array
     */
    private $enum = [
        'status' => [
            1 => 'approved',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier()
    {
        return $this->belongsTo('App\Entities\Carrier', 'carrier');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliateRequests()
    {
        return $this->hasMany('App\Entities\AffiliateRequest', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliates()
    {
        return $this->hasMany('App\Entities\Affiliate', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkouts()
    {
        return $this->hasMany('App\Entities\Checkout', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientsCookies()
    {
        return $this->hasMany('App\Entities\ClientsCookie', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function discountCoupons()
    {
        return $this->hasMany('App\Entities\DiscountCoupon', 'project');
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
        return $this->hasMany('App\Entities\ExtraMaterial', 'project');
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
    public function layouts()
    {
        return $this->hasMany('App\Entities\Layout', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pixels()
    {
        return $this->hasMany('App\Entities\Pixel', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plans()
    {
        return $this->hasMany('App\Entities\Plan', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany('App\Entities\Sale', 'project');
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
        return $this->hasMany('App\Entities\ShopifyIntegration', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersProjects()
    {
        return $this->hasMany('App\Entities\UserProject', 'project');
    }

    public function users()
    {
        return $this->belongsToMany('App\Entities\User', 'users_projects', 'project', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zenviaSms()
    {
        return $this->hasMany('App\Entities\ZenviaSms', 'project');
    }
}
