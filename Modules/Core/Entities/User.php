<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Events\ResetPasswordEvent;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\Notifiable;
use Laracasts\Presenter\PresentableTrait;
use Laravel\Passport\HasApiTokens;
use Modules\Core\Presenters\UserPresenter;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string $cellphone
 * @property string $document
 * @property string $zip_code
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $neighborhood
 * @property string $street
 * @property string $number
 * @property string $complement
 * @property string $photo
 * @property string $date_birth
 * @property boolean $address_document_status
 * @property boolean $personal_document_status
 * @property string $score
 * @property int $sms_zenvia_amount
 * @property string $percentage_rate
 * @property string $transaction_rate
 * @property string $foxcoin
 * @property string $email_amount
 * @property string $call_amount
 * @property string $updated_at
 * @property string $created_at
 * @property string $deleted_at
 * @property int $boleto_antecipation_money_days
 * @property int $credit_card_antecipation_money_days
 * @property int $release_money_days
 * @property int $percentage_antecipable
 * @property int $antecipation_tax
 * @property int $invites_amount
 * @property AffiliateRequest[] $affiliateRequests
 * @property Affiliate[] $affiliates
 * @property Company[] $companies
 * @property ConvertaxIntegration[] $convertaxIntegrations
 * @property HotzappIntegration[] $hotzappIntegrations
 * @property Invitation[] $invitations
 * @property NotazzIntegration[] $notazzIntegrations
 * @property Product[] $products
 * @property Sale[] $sales
 * @property ShopifyIntegration[] $shopifyIntegrations
 * @property Transfer[] $transfers
 * @property UserDocument[] $userDocuments
 */
class User extends Authenticable
{
    use Notifiable, HasRoles, HasApiTokens, SoftDeletes, PresentableTrait;
    /**
     * @var string
     */
    protected $presenter = UserPresenter::class;
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'remember_token',
        'cellphone',
        'document',
        'zip_code',
        'country',
        'state',
        'city',
        'neighborhood',
        'street',
        'number',
        'complement',
        'photo',
        'date_birth',
        'address_document_status',
        'personal_document_status',
        'score',
        'sms_zenvia_amount',
        'percentage_rate',
        'transaction_rate',
        'foxcoin',
        'email_amount',
        'call_amount',
        'boleto_antecipation_money_days',
        'credit_card_antecipation_money_days',
        'release_money_days',
        'percentage_antecipable',
        'antecipation_tax',
        'invites_amount',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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
    public function companies()
    {
        return $this->hasMany('Modules\Core\Entities\Company');
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
    public function hotzappIntegrations()
    {
        return $this->hasMany('Modules\Core\Entities\HotzappIntegration');
    }

    /**
     * @return HasMany
     */
    public function invitations()
    {
        return $this->hasMany('Modules\Core\Entities\Invitation', 'user_invited');
    }

    /**
     * @return HasMany
     */
    public function invites()
    {
        return $this->hasMany('Modules\Core\Entities\Invitation', 'invite');
    }

    /**
     * @return HasMany
     */
    public function notazzIntegrations()
    {
        return $this->hasMany('Modules\Core\Entities\NotazzIntegration');
    }

    /**
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany('Modules\Core\Entities\Product');
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany('Modules\Core\Entities\Sale', 'owner_id');
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
    public function smsMessages()
    {
        return $this->hasMany('Modules\Core\Entities\SmsMessage', 'user');
    }

    /**
     * @return HasMany
     */
    public function transfers()
    {
        return $this->hasMany('Modules\Core\Entities\Transfer');
    }

    /**
     * @return HasMany
     */
    public function userDocuments()
    {
        return $this->hasMany('Modules\Core\Entities\UserDocument');
    }

    /**
     * @return HasMany
     */
    public function userShoppings()
    {
        return $this->hasMany('Modules\Core\Entities\UserShopping', 'client');
    }

    /**
     * @return HasMany
     */
    public function usersProjects()
    {
        return $this->hasMany('Modules\Core\Entities\UsersProject');
    }

    /**
     * @return BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany('Modules\Core\Entities\Projects', 'users_projects', 'user_id', 'project_id');
    }

    public function sendPasswordResetNotification($token)
    {
        event(new ResetPasswordEvent($token, $this));
    }
}
