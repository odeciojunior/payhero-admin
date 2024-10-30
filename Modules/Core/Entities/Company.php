<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\PaginatableTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\CompanyPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

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
 * @property int $cielo_balance
 * @property int $asaas_balance
 * @property int $vega_balance
 * @property int $company_type
 * @property int $address_document_status
 * @property int $contract_document_status
 * @property int $boleto_release_money
 * @property int $credit_card_release_money
 * @property int $account_type
 * @property int $gateway_tax
 * @property int $credit_card_release_money_days
 * @property int $bank_slip_release_money_days
 * @property int $pix_release_money_days
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
 * @property json $id_wall_result
 * @method CompanyPresenter present()
 * @property int $order_priority
 * @property int $active_flag
 * @property int|null $has_pix_key
 * @property string $pix_key_situation
 * @property string|null $installment_tax
 * @property string|null $checkout_tax
 * //* @property string|null $extra_document
 * @property string|null $id_wall_date_update
 * @property string|null $transaction_tax
 * @property int $block_checkout
 * @property int|null $annual_income
 * @property json $situation
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

    public const GATEWAY_TAX_2 = 6.99;
    public const GATEWAY_TAX_15 = 5.99;
    public const GATEWAY_TAX_30 = 4.99;

    public const DEMO_ID = 1;
    public const DEMO_HASH_ID = "v2RmA83EbZPVpYB";

    public const SITUACTION_ACTIVE = 1;
    public const SITUACTION_SUSPENDED = 2;
    public const SITUACTION_UNFIT = 3;
    public const SITUACTION_DOWNLOADED = 4;
    public const SITUACTION_INVALID = 5;

    protected $presenter = CompanyPresenter::class;
    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = ["id_code"];

    protected $fillable = [
        "user_id",
        "fantasy_name",
        "document",
        "zip_code",
        "country",
        "state",
        "city",
        "street",
        "complement",
        "neighborhood",
        "number",
        "support_email",
        "support_telephone",
        "cielo_balance",
        "asaas_balance",
        "vega_balance",
        "address_document_status",
        "contract_document_status",
        "date_last_document_notification",
        "company_type",
        "order_priority",
        "capture_transaction_enabled",
        "account_type",
        "active_flag",
        "gateway_tax",
        "tax_default",
        "credit_card_tax",
        "credit_card_rule",
        "pix_tax",
        "pix_rule",
        "boleto_tax",
        "boleto_rule",
        "installment_tax",
        "checkout_tax",
        "credit_card_release_money_days",
        "credit_card_release_time",
        "credit_card_release_on_weekends",
        "bank_slip_release_money_days",
        "pix_release_money_days",
        "document_issue_date",
        "document_issuer",
        "document_issuer_state",
        "extra_document",
        "id_wall_result",
        "id_wall_date_update",
        "bureau_result",
        "transaction_tax",
        "block_checkout",
        "annual_income",
        "situation",
        "fantasy_name_custom",
        "document_custom",
        'observation',
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    protected $casts = [
        "situation" => "array",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\User");
    }

    public function affiliates(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\Affiliate");
    }

    public function companyDocuments(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\CompanyDocument");
    }

    public function hotzappIntegrations(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\HotzappIntegration");
    }

    public function invitations(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\Invitation");
    }

    public function transactions(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\Transaction");
    }

    public function transfers(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\Transfer");
    }

    public function usersProjects(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\UserProject");
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\Withdrawal");
    }

    public function companyBankAccounts(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\CompanyBankAccount");
    }

    public function gatewayCompanyCredential(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\GatewaysCompaniesCredential");
    }

    public function gatewayCredential($gateway_id)
    {
        return $this->gatewayCompanyCredential->where("gateway_id", $gateway_id)->first() ?? null;
    }

    public function getGatewayStatus($gateway_id)
    {
        return $this->gatewayCompanyCredential->where("gateway_id", $gateway_id)->first()->gateway_status ?? null;
    }

    public function getGatewaySubsellerId($gateway_id)
    {
        return $this->gatewayCompanyCredential->where("gateway_id", $gateway_id)->first()->gateway_subseller_id ?? null;
    }

    public function getGatewayApiKey($gatewayId)
    {
        return $this->gatewayCompanyCredential->where("gateway_id", $gatewayId)->first()->gateway_api_key ?? null;
    }

    public function gatewayBackofficeRequests(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\GatewaysBackofficeRequests");
    }

    public function companyBalance(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\CompanyBalance");
    }

    public function getDefaultBankAccount()
    {
        return $this->companyBankAccounts
            ->where("is_default", true)
            ->where("status", "VERIFIED")
            ->first() ?? null;
    }

    public function getBankAccountTED()
    {
        return $this->companyBankAccounts
            ->where("transfer_type", "TED")
            ->where("status", "VERIFIED")
            ->first() ?? null;
    }

    public function statusCompany()
    {
        //PF
        if ($this->company_type == self::PHYSICAL_PERSON) {
            return "Aprovado";
        }

        //PJ
        if ($this->statusCompanyJuridicalPerson() == "refused") {
            return "Recusado";
        } elseif ($this->statusCompanyJuridicalPerson() == "approved") {
            return "Aprovado";
        } else {
            return "Em análise";
        }
    }

    public function statusCompanyJuridicalPerson()
    {
        if (
            $this->contract_document_status === self::DOCUMENT_STATUS_APPROVED &&
            $this->address_document_status === self::DOCUMENT_STATUS_APPROVED
        ) {
            return "approved";
        } elseif (
            $this->contract_document_status === self::DOCUMENT_STATUS_PENDING ||
            $this->address_document_status === self::DOCUMENT_STATUS_PENDING
        ) {
            return "pending";
        } elseif (
            $this->contract_document_status === self::DOCUMENT_STATUS_REFUSED ||
            $this->address_document_status === self::DOCUMENT_STATUS_REFUSED
        ) {
            return "refused";
        } elseif (
            $this->contract_document_status === self::DOCUMENT_STATUS_ANALYZING ||
            $this->address_document_status === self::DOCUMENT_STATUS_ANALYZING
        ) {
            return "analyzing";
        }
    }

    public function getDocumentStatusAttribute()
    {
        if ($this->company_type == self::PHYSICAL_PERSON) {
            return self::DOCUMENT_STATUS_APPROVED;
        }

        if (
            $this->contract_document_status === self::DOCUMENT_STATUS_APPROVED &&
            $this->address_document_status === self::DOCUMENT_STATUS_APPROVED
        ) {
            return self::DOCUMENT_STATUS_APPROVED;
        } elseif (
            $this->contract_document_status === self::DOCUMENT_STATUS_PENDING ||
            $this->address_document_status === self::DOCUMENT_STATUS_PENDING
        ) {
            return self::DOCUMENT_STATUS_PENDING;
        } elseif (
            $this->contract_document_status === self::DOCUMENT_STATUS_REFUSED ||
            $this->address_document_status === self::DOCUMENT_STATUS_REFUSED
        ) {
            return self::DOCUMENT_STATUS_REFUSED;
        } elseif (
            $this->contract_document_status === self::DOCUMENT_STATUS_ANALYZING ||
            $this->address_document_status === self::DOCUMENT_STATUS_ANALYZING
        ) {
            return self::DOCUMENT_STATUS_ANALYZING;
        }

        return null;
    }
}
