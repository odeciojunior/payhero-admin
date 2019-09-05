<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Presenters\CompanyPresenter;

/**
 * @property int $id
 * @property int $user_id
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
 * @property integer $balance
 * @property boolean $bank_document_status
 * @property boolean $address_document_status
 * @property boolean $contract_document_status
 * @property string $created_at
 * @property string $deleted_at
 * @property string $updated_at
 * @property User $user
 * @property Affiliate[] $affiliates
 * @property Anticipation[] $anticipations
 * @property CompanyDocument[] $companyDocuments
 * @property HotzappIntegration[] $hotzappIntegrations
 * @property Invitation[] $invitations
 * @property Transaction[] $transactions
 * @property Transfer[] $transfers
 * @property UsersProject[] $usersProjects
 * @property Withdrawal[] $withdrawals
 */
class Company extends Model
{
    use SoftDeletes, PresentableTrait, FoxModelTrait;
    protected $presenter = CompanyPresenter::class;
    protected $appends = ['id_code'];
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliates()
    {
        return $this->hasMany('Modules\Core\Entities\Affiliate');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function anticipations()
    {
        return $this->hasMany('Modules\Core\Entities\Anticipation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companyDocuments()
    {
        return $this->hasMany('Modules\Core\Entities\CompanyDocument');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hotzappIntegrations()
    {
        return $this->hasMany('Modules\Core\Entities\HotzappIntegration', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->hasMany('Modules\Core\Entities\Invitation', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany('Modules\Core\Entities\Transaction');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfers()
    {
        return $this->hasMany('Modules\Core\Entities\Transfer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersProjects()
    {
        return $this->hasMany('Modules\Core\Entities\UsersProject');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function withdrawals()
    {
        return $this->hasMany('Modules\Core\Entities\Withdrawal');
    }
}
