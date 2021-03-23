<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Passport\Client;
use Laravel\Passport\Token;
use Modules\Core\Events\ResetPasswordEvent;
use Modules\Core\Presenters\UserPresenter;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\CausesActivity;
use App\Traits\LogsActivity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Laracasts\Presenter\PresentableTrait;
use Laravel\Passport\HasApiTokens;

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
 * @property string|null $sex
 * @property string|null $mother_name
 * @property int $has_sale_before_getnet
 * @property int $onboarding
 * @property string|null $observation
 * @property int $account_is_approved
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @mixin \Eloquent
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
     * @param  Activity  $activity
     * @param  string  $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'Usuário '.$this->name.' foi deletedo.';
        } elseif ($eventName == 'updated') {
            $activity->description = 'Usuário '.$this->name.' foi atualizado.';
        } elseif ($eventName == 'created') {
            $activity->description = 'Usuário '.$this->name.' foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return HasMany
     */
    public function affiliateRequests()
    {
        return $this->hasMany('Modules\Core\Entities\AffiliateRequest');
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
    public function companies()
    {
        return $this->hasMany('Modules\Core\Entities\Company');
    }

    /**
     * @return HasMany
     */
    public function convertaxIntegrations()
    {
        return $this->hasMany('Modules\Core\Entities\ConvertaxIntegration');
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
        return $this->hasMany('Modules\Core\Entities\Invitation', 'user_invited');
    }

    /**
     * @return HasMany
     */
    public function invites()
    {
        return $this->hasMany('Modules\Core\Entities\Invitation', 'invite');
    }

    /**
     * @return HasMany
     */
    public function notazzIntegrations()
    {
        return $this->hasMany('Modules\Core\Entities\NotazzIntegration');
    }

    /**
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany('Modules\Core\Entities\Product');
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany('Modules\Core\Entities\Sale', 'owner_id');
    }

    /**
     * @return HasMany
     */
    public function shopifyIntegrations()
    {
        return $this->hasMany('Modules\Core\Entities\ShopifyIntegration');
    }

    /**
     * @return HasMany
     */
    public function smsMessages()
    {
        return $this->hasMany('Modules\Core\Entities\SmsMessage', 'user');
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
    public function userDocuments()
    {
        return $this->hasMany('Modules\Core\Entities\UserDocument');
    }

    /**
     * @return HasMany
     */
    public function userShoppings()
    {
        return $this->hasMany('Modules\Core\Entities\UserShopping', 'client');
    }

    /**
     * @return HasMany
     */
    public function usersProjects()
    {
        return $this->hasMany('Modules\Core\Entities\UserProject');
    }

    /**
     * @return BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany('Modules\Core\Entities\Projects', 'users_projects', 'user_id', 'project_id');
    }

    /**
     * @param  string  $token
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
        return $this->hasMany('Modules\Core\Entities\UserDevice');
    }

    /**
     * @return HasMany
     */
    public function userTerms()
    {
        return $this->hasMany('Modules\Core\Entities\UserTerms');
    }

}
