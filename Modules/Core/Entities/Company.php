<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\PaginatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\CompanyPresenter;
use App\Traits\LogsActivity;

/**
 * Class Company
 * @package Modules\Core\Entities
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
 * @property int $bank_document_status
 * @property int $address_document_status
 * @property int $contract_document_status
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
 * @property UserProject[] $usersProjects
 * @property Withdrawal[] $withdrawals
 * @property string $bank_document_status_value
 * @property string $bank_document_status_badge
 */
class Company extends Model
{
    use SoftDeletes, PaginatableTrait, PresentableTrait, FoxModelTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = CompanyPresenter::class;
    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [
        'id_code',
    ];
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
        'company_type',
        'created_at',
        'deleted_at',
        'updated_at',
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
     * Registra apenas os atributos alterados no log
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
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
    public function anticipations()
    {
        return $this->hasMany('Modules\Core\Entities\Anticipation');
    }

    /**
     * @return HasMany
     */
    public function companyDocuments()
    {
        return $this->hasMany('Modules\Core\Entities\CompanyDocument');
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
        return $this->hasMany('Modules\Core\Entities\Invitation');
    }

    /**
     * @return HasMany
     */
    public function transactions()
    {
        return $this->hasMany('Modules\Core\Entities\Transaction');
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
    public function usersProjects()
    {
        return $this->hasMany('Modules\Core\Entities\UserProject');
    }

    /**
     * @return HasMany
     */
    public function withdrawals()
    {
        return $this->hasMany('Modules\Core\Entities\Withdrawal');
    }
}
