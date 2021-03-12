<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Modules\Core\Events\ResetPasswordEvent;
use Modules\Core\Presenters\UserPresenter;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\CausesActivity;
use App\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Laracasts\Presenter\PresentableTrait;
use Laravel\Passport\HasApiTokens;

/**
 * @property integer $id
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
 * @property string $transaction_rate
 * @property boolean $account_is_approved
 * @property string $id_wall_result
 * @property string $sex
 * @property string $mother_name
 * @property boolean $has_sale_before_getnet
 * @property integer $chargeback_rate
 * @property integer $account_score
 * @property integer $chargeback_score
 * @property integer $attendance_score
 * @property integer $tracking_score
 * @property integer $installment_cashback
 * @property integer $level
 * @property integer $total_commission_value
 * @property integer $attendance_average_response_time
 * @property string $updated_at
 * @property string $created_at
 * @property string $deleted_at
 * @property integer $invites_amount
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

    /**
     * @var string
     */
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
        'invites_amount',
        'last_login',
        'account_owner_id',
        'deleted_project_filter',
        'id_wall_result',
        'sex',
        'mother_name',
        'has_sale_before_getnet',
        'account_is_approved',
        'chargeback_rate',
        'account_score',
        'chargeback_score',
        'attendance_score',
        'tracking_score',
        'attendance_average_response_time',
        'installment_cashback',
        'level',
        'total_commission_value',
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
     * @return HasManyThrough
     */
    public function benefits()
    {
        return $this->hasManyThrough(Benefit::class, UserBenefit::class, 'user_id', 'id', 'id', 'benefit_id')
            ->join('users', 'users.id', '=', 'user_benefits.user_id')
            ->select('benefits.*', 'user_benefits.disabled', 'users.installment_cashback');

    }

}
