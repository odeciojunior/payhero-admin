<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user
 * @property string $fantasy_name
 * @property string $company_document
 * @property string $zip_code
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $street
 * @property string $complement
 * @property string $neighborhood
 * @property string $agency
 * @property string $bank
 * @property string $number
 * @property string $agency_digit
 * @property string $account
 * @property string $account_digit
 * @property string $statement_descriptor
 * @property string $shortened_descriptor
 * @property string $business_website
 * @property string $support_email
 * @property string $support_telephone
 * @property string $created_at
 * @property string $deleted_at
 * @property string $updated_at
 * @property User $user
 * @property Affiliate[] $affiliates
 * @property HotzappIntegration[] $hotzappIntegrations
 * @property Invitation[] $invitations
 * @property Plan[] $plans
 * @property Transaction[] $transactions
 * @property UsersProject[] $usersProjects
 */
class Company extends Model
{
    use SoftDeletes;
    use FoxModelTrait;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'fantasy_name',
        'company_document',
        'zip_code',
        'country',
        'state',
        'city',
        'street',
        'complement',
        'neighborhood',
        'agency',
        'bank',
        'number',
        'agency_digit',
        'account',
        'account_digit',
        'statement_descriptor',
        'shortened_descriptor',
        'business_website',
        'support_email',
        'support_telephone',
        'balance',
        'bank_document_status',
        'address_document_status',
        'contract_document_status',
        'created_at',
        'deleted_at',
        'updated_at',
    ];
    /**
     * @var array
     */
    private $enum = [
        'document_type'            => [
            1 => 'bank_document_status',
            2 => 'address_document_status',
            3 => 'contract_document_status',
        ],
        'bank_document_status'     => [
            1 => 'pending',
            2 => 'analyzing',
            3 => 'approved',
            4 => 'refused',
        ],
        'address_document_status'  => [
            1 => 'pending',
            2 => 'analyzing',
            3 => 'approved',
            4 => 'refused',
        ],
        'contract_document_status' => [
            1 => 'pending',
            2 => 'analyzing',
            3 => 'approved',
            4 => 'refused',
        ],
        'status'                   => [
            1 => 'pending',
            2 => 'analyzing',
            3 => 'approved',
            4 => 'refused',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Entities\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliates()
    {
        return $this->hasMany('App\Entities\Affiliate', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hotzappIntegrations()
    {
        return $this->hasMany('App\Entities\HotzappIntegration', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->hasMany('App\Entities\Invitation', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plans()
    {
        return $this->hasMany('App\Entities\Plan', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany('App\Entities\Transaction', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersProjects()
    {
        return $this->hasMany('App\Entities\UsersProject', 'company');
    }
}
