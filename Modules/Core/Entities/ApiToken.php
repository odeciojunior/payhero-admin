<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\PaginatableTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\Exceptions\PresenterException;
use Laracasts\Presenter\PresentableTrait;
use Laravel\Passport\Passport;
use Laravel\Passport\PersonalAccessTokenFactory;
use Laravel\Passport\PersonalAccessTokenResult;
use Laravel\Passport\Token;
use Modules\Core\Presenters\ApiTokenPresenter;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class ApiToken
 * @property integer $id
 * @property int $user_id
 * @property string $token_id
 * @property string $access_token
 * @property string $scopes
 * @property int $integration_type_enum
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property User $user
 * @property Token $token
 * @method ApiTokenPresenter present()
 * @package Modules\Core\Entities
 */
class ApiToken extends Model
{
    use FoxModelTrait, PaginatableTrait, PresentableTrait, SoftDeletes, LogsActivity;
    const TOKEN_SCOPE_ADMIN   = "admin";
    const TOKEN_SCOPE_USER    = "user";
    const TOKEN_SCOPE_SALE    = "sale";
    const TOKEN_SCOPE_PRODUCT = "product";
    const TOKEN_SCOPE_CLIENT  = "client";
    /**
     * @var array
     */
    public static $tokenScopes = [
        self::TOKEN_SCOPE_ADMIN   => 'Admin Full Access',
        self::TOKEN_SCOPE_USER    => 'Data User Access',
        self::TOKEN_SCOPE_SALE    => 'Data Sale Access',
        self::TOKEN_SCOPE_PRODUCT => 'Data Product Access',
        self::TOKEN_SCOPE_CLIENT  => 'Data Client Access',
    ];
    /**
     * @var string
     */
    protected $presenter = ApiTokenPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token_id',
        'access_token',
        'scopes',
        'integration_type_enum',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * The attributes that should be hidden for serialization.
     * @var array
     */
    protected $hidden = [
        'user_id',
        'token_id',
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
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the access tokens for the user.
     * @return BelongsTo
     */
    public function token()
    {
        return $this->belongsTo(Passport::tokenModel());
    }

    /**
     * @return PersonalAccessTokenResult
     * @throws BindingResolutionException
     * @throws PresenterException
     */
    public function getValidToken()
    {
        if (empty($this->access_token) || empty($this->token_id)) {
            return null;
        }
        /** @var Token $token */
        $token = $this->token;
        if (empty($token) | ($token->revoked ?? false) || ($token->expires_at < now())) {
            return self::generateTokenIntegration($this->description, $this->present()->getTokenScope());
        }

        return new PersonalAccessTokenResult($this->access_token, $token);
    }

    /**
     * @param string $name
     * @param string|array $scopes
     * @param User $user
     * @return PersonalAccessTokenResult
     * @throws BindingResolutionException
     */
    public static function generateTokenIntegration($name, $scopes, $user = null)
    {
        $scopes = is_array($scopes) ? $scopes : [$scopes];
        $userId = $user->account_owner_id ?? auth()->user()->account_owner_id;
        /** @var PersonalAccessTokenFactory $tokenFactory */
        $tokenFactory = app()->make(PersonalAccessTokenFactory::class);

        return $tokenFactory->make($userId, $name, $scopes);
    }
}
