<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\LogsActivity;
use App\Traits\PaginatableTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\CompanyPresenter;
use Spatie\Activitylog\Models\Activity;

/**
 * Class Company
 *
 * @package Modules\Core\Entities
 * @property int $id
 * @property int $user_id
 * @property string $fantasy_name
 * @property string $document
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
 * @property string $support_email
 * @property string $support_telephone
 * @property int $balance
 * @property int $company_type
 * @property int $bank_document_status
 * @property int $address_document_status
 * @property int $contract_document_status
 * @property string $subseller_getnet_id
 * @property string $subseller_getnet_homolog_id
 * @property int $get_net_status
 * @property int $boleto_release_money
 * @property int $credit_card_release_money
 * @property int $capture_transaction_enabled
 * @property int $account_type
 * @property int $gateway_tax
 * @property int $gateway_release_money_days
 * @property string $document_issue_date
 * @property string $document_issuer
 * @property string $document_issuer_state
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
 * @property json $id_wall_result
 * @method CompanyPresenter present()
 * @property int $order_priority
 * @property int $active_flag
 * @property int|null $has_pix_key
 * @property string $pix_key_situation
 * @property string|null $installment_tax
 * @property string|null $extra_document
 * @property string|null $id_wall_date_update
 * @property string|null $transaction_rate
 * @property int $block_checkout
 * @property int|null $annual_income
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read int|null $affiliates_count
 * @property-read int|null $company_documents_count
 * @property-read string $id_code
 * @property-read int|null $hotzapp_integrations_count
 * @property-read int|null $invitations_count
 * @property-read int|null $transactions_count
 * @property-read int|null $transfers_count
 * @property-read int|null $users_projects_count
 * @property-read int|null $withdrawals_count
 */
class Company extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PaginatableTrait;
    use PresentableTrait;
    use SoftDeletes;

    public const PHYSICAL_PERSON = 1;
    public const JURIDICAL_PERSON = 2;

    public const STATUS_PENDING = 1;
    public const STATUS_ANALYZING = 2;
    public const STATUS_APPROVED = 3;
    public const STATUS_REFUSED = 4;

    public const DOCUMENT_STATUS_PENDING = 1;
    public const DOCUMENT_STATUS_ANALYZING = 2;
    public const DOCUMENT_STATUS_APPROVED = 3;
    public const DOCUMENT_STATUS_REFUSED = 4;

    public const GETNET_STATUS_APPROVED = 1;
    public const GETNET_STATUS_REVIEW = 2;
    public const GETNET_STATUS_REPROVED = 3;
    public const GETNET_STATUS_APPROVED_GETNET = 4;
    public const GETNET_STATUS_ERROR = 5;
    public const GETNET_STATUS_PENDING = 6;

    public const GATEWAY_TAX = 6.9;

    protected $presenter = CompanyPresenter::class;
    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = ['id_code'];

    protected $fillable = [
        'user_id',
        'fantasy_name',
        'document',
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
        'support_email',
        'support_telephone',
        'balance',
        'bank_document_status',
        'address_document_status',
        'contract_document_status',
        'company_type',
        'pix_key_situation',
        'has_pix_key',
        'order_priority',
        'get_net_status',
        'capture_transaction_enabled',
        'subseller_getnet_id',
        'subseller_getnet_homolog_id',
        'account_type',
        'extra_document',
        'document_issue_date',
        'document_issuer',
        'document_issuer_state',
        'active_flag',
        'gateway_tax',
        'installment_tax',
        'gateway_release_money_days',
        'transaction_rate',
        'deleted_at',
        'created_at',
        'updated_at',
        'id_wall_result',
        'block_checkout',
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

    public function tapActivity(Activity $activity, string $eventName)
    {
        switch ($eventName) {
            case 'deleted':
                $activity->description = "Empresa {$this->fantasy_name} foi deletado.";
                break;
            case 'updated':
                $activity->description = "Empresa {$this->fantasy_name}  foi atualizado.";
                break;
            case 'created':
                $activity->description = "Empresa {$this->fantasy_name} foi criado.";
                break;
            default:
                $activity->description = $eventName;
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }

    public function affiliates(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\Affiliate');
    }

    public function companyDocuments(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\CompanyDocument');
    }

    public function hotzappIntegrations(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\HotzappIntegration');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\Invitation');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\Transaction');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\Transfer');
    }

    public function usersProjects(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\UserProject');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\Withdrawal');
    }
}
