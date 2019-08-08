<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Laravel\Passport\HasApiTokens;
use Modules\Core\Events\ResetPasswordEvent;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string $celphone
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
 * @property string $score
 * @property int $sms_zenvia_amount
 * @property string $percentage_rate
 * @property string $transaction_rate
 * @property string $foxcoin
 * @property string $email_amount
 * @property string $call_amount
 * @property int $boleto_antecipation_money_days
 * @property int $credit_card_antecipation_money_days
 * @property int $release_money_days
 * @property int $percentage_antecipable
 * @property int $antecipation_tax
 * @property string $updated_at
 * @property string $created_at
 * @property string $deleted_at
 * @property AffiliateRequest[] $affiliateRequests
 * @property Affiliate[] $affiliates
 * @property Company[] $companies
 * @property Invitation[] $invitations
 * @property Invitation[] $invitations
 * @property Product[] $products
 * @property Sale[] $sales
 * @property ShopifyIntegration[] $shopifyIntegrations
 * @property SmsMessage[] $smsMessages
 * @property Transfer[] $transfers
 * @property UserShopping[] $userShoppings'
 * @property UsersProject[] $usersProjects
 */
class User extends Authenticable
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;
    use FoxModelTrait;
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
        'updated_at',
        'created_at',
        'deleted_at',
    ];
    /**
     * @var array
     */
    private $enum = [
        'document_type'            => [
            1 => 'personal_document',
            2 => 'address_document',
        ],
        'address_document_status'  => [
            1 => 'pending',
            2 => 'analyzing',
            3 => 'approved',
            4 => 'refused',
        ],
        'personal_document_status' => [
            1 => 'pending',
            2 => 'analyzing',
            3 => 'approved',
            4 => 'refused',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliateRequests()
    {
        return $this->hasMany('App\Entities\AffiliateRequest', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliates()
    {
        return $this->hasMany('App\Entities\Affiliate', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companies()
    {
        return $this->hasMany('App\Entities\Company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->hasMany('App\Entities\Invitation', 'user_invited');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('App\Entities\Product', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany('App\Entities\Sale', 'owner');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopifyIntegrations()
    {
        return $this->hasMany('App\Entities\ShopifyIntegration', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function smsMessages()
    {
        return $this->hasMany('App\Entities\SmsMessage', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfers()
    {
        return $this->hasMany('App\Entities\Transfer', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userShoppings()
    {
        return $this->hasMany('App\Entities\UserShopping', 'client');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersProjects()
    {
        return $this->hasMany('App\Entities\UserProject', 'user');
    }

    public function projects()
    {
        return $this->belongsToMany('App\Entities\User', 'users_projects', 'user', 'project');
    }

    public function sendPasswordResetNotification($token)
    {
        event(new ResetPasswordEvent($token, $this));
    }
}
