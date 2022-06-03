<?php

namespace Modules\Core\Entities;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\InvitePresenter;
use Spatie\Activitylog\Models\Activity;

/**
 * @property int $id
 * @property int $invite
 * @property int $user_invited
 * @property int $company_id
 * @property int $invitation_id
 * @property string $email_invited
 * @property int $status
 * @property string $register_date
 * @property string $expiration_date
 * @property string $parameter
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Company $company
 * @property Invitation $invitation
 * @property User $user
 */
class Invitation extends Model
{
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;
    use HasFactory;

    public const INVITATION_ACCEPTED = 1;
    public const INVITATION_PENDING = 2;
    public const INVITATION_EXPIRED = 3;

    public const STATUS_ACTIVE = 1;
    public const STATUS_DISABLED = 0;

    /**
     * @var string
     */
    protected $presenter = InvitePresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'invite',
        'user_invited',
        'company_id',
        'invitation_id',
        'email_invited',
        'status',
        'register_date',
        'expiration_date',
        'parameter',
        'created_at',
        'updated_at',
        'deleted_at',
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
        switch ($eventName) {
            case 'deleted':
                $activity->description = 'Convite deletedo.';
                break;
            case 'updated':
                $activity->description = 'Convite foi atualizado.';
                break;
            case 'created':
                $activity->description = 'Convite foi criado.';
                break;
            default:
                $activity->description = $eventName;
        }
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function userInvited(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_invited');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'invite');
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
