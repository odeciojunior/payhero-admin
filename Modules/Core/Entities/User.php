<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use Laravel\Passport\HasApiTokens;
use Modules\Core\Events\ResetPasswordEvent;
use Modules\Core\Presenters\UserPresenter;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * Modules\Core\Entities\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $email_verified
 * @property int $status
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $cellphone
 * @property int $cellphone_verified
 * @property string|null $document
 * @property string|null $zip_code
 * @property string|null $country
 * @property string|null $state
 * @property string|null $city
 * @property string|null $neighborhood
 * @property string|null $street
 * @property string|null $number
 * @property string|null $complement
 * @property string|null $photo
 * @property string|null $date_birth
 * @property int $address_document_status
 * @property int $personal_document_status
 * @property string|null $last_login
 * @property int $invites_amount
 * @property int|null $account_owner_id
 * @property int $deleted_project_filter
 * @property mixed|null $id_wall_result
 * @property mixed|null $bureau_result
 * @property string|null $sex
 * @property string|null $mother_name
 * @property bool $has_sale_before_getnet
 * @property bool $show_old_finances
 * @property int $onboarding
 * @property string|null $observation
 * @property int $account_is_approved
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $transaction_rate
 * @property int $chargeback_rate
 * @property int $account_score
 * @property int $chargeback_score
 * @property int $attendance_score
 * @property int $tracking_score
 * @property int $installment_cashback
 * @property bool $get_faster
 * @property int $release_count
 * @property bool $has_security_reserve
 * @property int $level
 * @property bool $ignore_automatic_benefits_updates
 * @property int $total_commission_value
 * @property int $attendance_average_response_time
 * @property string $mkt_information
 * @property boolean $block_attendance_balance
 * @property Collection $affiliateRequests
 * @property Collection $affiliates
 * @property Collection $companies
 * @property Collection $convertaxIntegrations
 * @property Collection $hotzappIntegrations
 * @property Collection $invitations
 * @property Collection $notazzIntegrations
 * @property Collection $products
 * @property Collection $sales
 * @property Collection $shopifyIntegrations
 * @property Collection $transfers
 * @property Collection $userDocuments
 * @property Collection $achievements
 * @property Collection $tasks
 * @property Collection $benefits
 * @method UserPresenter present()
 */
class User extends Authenticable
{
    use CausesActivity;
    use FoxModelTrait;
    use HasApiTokens;
    use HasRoles;
    use LogsActivity;
    use Notifiable;
    use PresentableTrait;
    use SoftDeletes;

    public const STATUS_ACTIVE = 1;
    public const STATUS_WITHDRAWAL_BLOCKED = 2;
    public const STATUS_ACCOUNT_BLOCKED = 3;
    public const STATUS_ACCOUNT_FROZEN = 4;

    public const DOCUMENT_STATUS_PENDING = 1;
    public const DOCUMENT_STATUS_ANALYZING = 2;
    public const DOCUMENT_STATUS_APPROVED = 3;
    public const DOCUMENT_STATUS_REFUSED = 4;

    public const CELLPHONE_VERIFIED = 1;
    public const EMAIL_VERIFIED = 1;

    protected $presenter = UserPresenter::class;
    /**
     * @var array
     */
    protected $appends = ['id_code'];
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified',
        'status',
        'password',
        'remember_token',
        'cellphone',
        'cellphone_verified',
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
        'date_last_document_notification',
        'invites_amount',
        'last_login',
        'account_owner_id',
        'deleted_project_filter',
        'id_wall_result',
        'bureau_result',
        'sex',//
        'mother_name',//
        'has_sale_before_getnet',//
        'account_is_approved',
        'chargeback_rate',
        'account_score',
        'chargeback_score',
        'attendance_score',
        'tracking_score',
        'attendance_average_response_time',
        'installment_cashback',
        'get_faster',
        'release_count',
        'has_security_reserve',
        'security_reserve_rule',
        'level',
        'ignore_automatic_benefits_updates',
        'total_commission_value',
        'show_old_finances',//
        'mkt_information',
        'block_attendance_balance',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * @var array
     */
    protected static $logAttributes = ['*'];
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
     * Ignora atributos
     * @var array
     */
    protected static $logAttributesToIgnore = ['last_login', 'updated_at'];

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'Usuário ' . $this->name . ' foi deletado.';
        } elseif ($eventName == 'updated') {
            $activity->description = 'Usuário ' . $this->name . ' foi atualizado.';
        } elseif ($eventName == 'created') {
            $activity->description = 'Usuário ' . $this->name . ' foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return HasMany
     */
    public function affiliateRequests()
    {
        return $this->hasMany(AffiliateRequest::class);
    }

    /**
     * @return HasMany
     */
    public function affiliates()
    {
        return $this->hasMany(Affiliate::class);
    }

    /**
     * @return HasMany
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    /**
     * @return HasMany
     */
    public function convertaxIntegrations()
    {
        return $this->hasMany(ConvertaxIntegration::class);
    }

    /**
     * @return HasMany
     */
    public function hotzappIntegrations()
    {
        return $this->hasMany(HotzappIntegration::class);
    }

    /**
     * @return HasMany
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'user_invited');
    }

    /**
     * @return HasMany
     */
    public function invites()
    {
        return $this->hasMany(Invitation::class, 'invite');
    }

    /**
     * @return HasMany
     */
    public function notazzIntegrations()
    {
        return $this->hasMany(NotazzIntegration::class);
    }

    /**
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, 'owner_id');
    }

    /**
     * @return HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return HasMany
     */
    public function shopifyIntegrations()
    {
        return $this->hasMany(ShopifyIntegration::class);
    }

    /**
     * @return HasMany
     */
    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    /**
     * @return HasMany
     */
    public function userDocuments()
    {
        return $this->hasMany(UserDocument::class);
    }

    /**
     * @return HasMany
     */
    public function userShoppings()
    {
        return $this->hasMany(UserShopping::class, 'client');
    }

    /**
     * @return HasMany
     */
    public function usersProjects()
    {
        return $this->hasMany(UserProject::class);
    }

    /**
     * @return BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'users_projects', 'user_id', 'project_id');
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        event(new ResetPasswordEvent($token, $this));
    }

    /**
     * @return HasOne
     */
    public function userNotification()
    {
        return $this->hasOne(UserNotification::class);
    }

    /**
     * @return HasOne
     */
    public function userInformations()
    {
        return $this->belongsTo(UserInformation::class, 'document', 'document');
    }

    /**
     * @return HasMany
     */
    public function userDevices()
    {
        return $this->hasMany(UserDevice::class);
    }

    /**
     * @return HasMany
     */
    public function userTerms()
    {
        return $this->hasMany(UserTerms::class);
    }

    /**
     * @return BelongsToMany
     */
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tasks()
    {
        return $this->belongsToMany(
            Task::class,
            'tasks_users',
            'user_id',
            'task_id'
        );
    }

    /**
     * @return HasMany
     */
    public function benefits()
    {
        return $this->hasMany(UserBenefit::class)
            ->join('benefits', 'benefits.id', '=', 'user_benefits.benefit_id')
            ->select('user_benefits.*', 'benefits.name', 'benefits.description', 'benefits.level');
    }
}
