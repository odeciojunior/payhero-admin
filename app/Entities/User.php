<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

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
 * @property AffiliateRequest[] $affiliateRequests
 * @property Affiliate[] $affiliates
 * @property Company[] $companies
 * @property ConvertaxIntegration[] $convertaxIntegrations
 * @property HotzappIntegration[] $hotzappIntegrations
 * @property Invitation[] $invitations
 * @property Invitation[] $invitations
 * @property NotazzIntegration[] $notazzIntegrations
 * @property Product[] $products
 * @property Sale[] $sales
 * @property ShopifyIntegration[] $shopifyIntegrations
 * @property SmsMessage[] $smsMessages
 * @property Transfer[] $transfers
 * @property UserDocument[] $userDocuments
 * @property UserShopping[] $userShoppings
 * @property UsersProject[] $usersProjects
 */
class User extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'remember_token', 'cellphone', 'document', 'zip_code', 'country', 'state', 'city', 'neighborhood', 'street', 'number', 'complement', 'photo', 'date_birth', 'address_document_status', 'personal_document_status', 'score', 'sms_zenvia_amount', 'percentage_rate', 'transaction_rate', 'foxcoin', 'email_amount', 'call_amount', 'updated_at', 'created_at', 'deleted_at', 'boleto_antecipation_money_days', 'credit_card_antecipation_money_days', 'release_money_days', 'percentage_antecipable', 'antecipation_tax'];

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
    public function companies()
    {
        return $this->hasMany('App\Entities\Company');
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
    public function hotzappIntegrations()
    {
        return $this->hasMany('App\Entities\HotzappIntegration');
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
    public function invitations()
    {
        return $this->hasMany('App\Entities\Invitation', 'invite');
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
    public function products()
    {
        return $this->hasMany('App\Entities\Product');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany('App\Entities\Sale', 'owner_id');
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
    public function smsMessages()
    {
        return $this->hasMany('App\Entities\SmsMessage', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfers()
    {
        return $this->hasMany('App\Entities\Transfer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userDocuments()
    {
        return $this->hasMany('App\Entities\UserDocument');
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
        return $this->hasMany('App\Entities\UsersProject');
    }
}
