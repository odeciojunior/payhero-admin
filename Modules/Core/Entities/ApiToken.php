<?php

declare(strict_types=1);

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\PaginatableTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\Exceptions\PresenterException;
use Laracasts\Presenter\PresentableTrait;
use Laravel\Passport\Passport;
use Laravel\Passport\PersonalAccessTokenFactory;
use Laravel\Passport\PersonalAccessTokenResult;
use Laravel\Passport\Token;
use Modules\Core\Presenters\ApiTokenPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class ApiToken
 * @property int $id
 * @property int $user_id
 * @property int $company_id
 * @property int $project_id
 * @property string $token_id
 * @property string $access_token
 * @property string $scopes
 * @property string $platform_enum
 * @property int $integration_type_enum
 * @property string $description
 * @property string $postback
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
    use FoxModelTrait;
    use HasFactory;
    use LogsActivity;
    use PaginatableTrait;
    use PresentableTrait;
    use SoftDeletes;

    public const TOKEN_SCOPE_ADMIN = "admin";
    public const TOKEN_SCOPE_USER = "user";
    public const TOKEN_SCOPE_SALE = "sale";
    public const TOKEN_SCOPE_PRODUCT = "product";
    public const TOKEN_SCOPE_CLIENT = "client";

    public const INTEGRATION_TYPE_ADMIN = 1;
    public const INTEGRATION_TYPE_PERSONAL = 2;
    public const INTEGRATION_TYPE_EXTERNAL = 3;
    public const INTEGRATION_TYPE_CHECKOUT_API = 4;
    public const INTEGRATION_TYPE_SPLIT_API = 5;

    public const PLATFORM_ENUM_VEGA_CHECKOUT = "VEGA_CHECKOUT";
    public const PLATFORM_ENUM_GR_SOLUCOES = "GR_SOLUCOES";
    public const PLATFORM_ENUM_ADOOREI_CHECKOUT = "ADOOREI_CHECKOUT";
    public const PLATFORM_ENUM_WEBAPI = "WEBAPI";

    /**
     * @var array
     */
    public static $tokenScopes = [
        self::TOKEN_SCOPE_ADMIN => "Admin Full Access",
        self::TOKEN_SCOPE_USER => "Data User Access",
        self::TOKEN_SCOPE_SALE => "Data Sale Access",
        self::TOKEN_SCOPE_PRODUCT => "Data Product Access",
        self::TOKEN_SCOPE_CLIENT => "Data Client Access",
    ];

    /**
     * @var string
     */
    protected $presenter = ApiTokenPresenter::class;

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'project_id',
        'token_id',
        'access_token',
        'scopes',
        'integration_type_enum',
        'description',
        'postback',
        'platform_enum',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array
     */
    protected $hidden = ["user_id", "token_id"];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    public function project(): hasOne
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
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
        if (empty($token) | ($token->revoked ?? false) || $token->expires_at < now()) {
            return self::generateTokenIntegration($this->description, $this->present()->getTokenScope());
        }

        return new PersonalAccessTokenResult($this->access_token, $token);
    }

    /**
     * @param  string  $name
     * @param  string|array  $scopes
     * @param  User  $user
     * @return PersonalAccessTokenResult
     * @throws BindingResolutionException
     */
    public static function generateTokenIntegration($name, $scopes, $user = null)
    {
        $scopes = is_array($scopes) ? $scopes : [$scopes];
        $userId = $user->account_owner_id ?? auth()->user()->account_owner_id;
        /** @var PersonalAccessTokenFactory $tokenFactory */
        Passport::personalAccessTokensExpireIn(now()->addYears(30));
        $tokenFactory = app()->make(PersonalAccessTokenFactory::class);
        Passport::personalAccessTokensExpireIn(now()->addDays(1));

        return $tokenFactory->make($userId, $name, $scopes);
    }
}
