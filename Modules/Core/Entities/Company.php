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
use Spatie\Activitylog\Models\Activity;

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
 * @property int $balance
 * @property int $company_type
 * @property int $bank_document_status
 * @property int $address_document_status
 * @property int $contract_document_status
 * @property int $patrimony
 * @property string $state_fiscal_document_number
 * @property string $business_entity_type
 * @property string $economic_activity_classification_code
 * @property int $monthly_gross_income
 * @property int $federal_registration_status
 * @property string $founding_date
 * @property int $subseller_getnet_id
 * @property int $account_type
 * @property int $social_value
 * @property string $federal_registration_status_date
 * @property string $document_issue_date
 * @property string $document_issuer
 * @property string document_issuer_state
 * @property string $created_at
 * @property string $deleted_at
 * @property string $updated_at
 * @property User $user
 * @property Affiliate[] $affiliates
 * @property CompanyDocument[] $companyDocuments
 * @property HotzappIntegration[] $hotzappIntegrations
 * @property Invitation[] $invitations
 * @property Transaction[] $transactions
 * @property Transfer[] $transfers
 * @property UserProject[] $usersProjects
 * @property Withdrawal[] $withdrawals
 * @property string $bank_document_status_value
 * @property string $bank_document_status_badge
 * @property json id_wall_result
 * @method CompanyPresenter present()
 */
class Company extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PaginatableTrait;
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = CompanyPresenter::class;
    /**
     * The accessors to append to the model's array form.
     * @var array
     */
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
        'company_type',
        'order_priority',
        'patrimony',
        'state_fiscal_document_number',
        'business_entity_type',
        'economic_activity_classification_code',
        'monthly_gross_income',
        'federal_registration_status',
        'get_net_status',
        'founding_date',
        'subseller_getnet_id',
        'account_type',
        'social_value',
        'federal_registration_status_date',
        'document_number',
        'document_issue_date',
        'document_issuer',
        'document_issuer_state',
        'active_flag',
        'deleted_at',
        'created_at',
        'updated_at',
        'braspag_status',
        'braspag_merchant_id',
        'braspag_merchant_homolog_id',
        'id_wall_result'
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
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'Empresa ' . $this->fantasy_name . ' foi deletedo.';
        } elseif ($eventName == 'updated') {
            $activity->description = 'Empresa ' . $this->fantasy_name . ' foi atualizado.';
        } elseif ($eventName == 'created') {
            $activity->description = 'Empresa ' . $this->fantasy_name . ' foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

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
