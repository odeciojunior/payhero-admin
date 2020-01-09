<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\InvitePresenter;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
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
    use SoftDeletes, PresentableTrait, LogsActivity;
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
        if ($eventName == 'deleted') {
            $activity->description = 'Convite deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Convite foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Convite foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('Modules\Core\Entities\Company', 'company_id');
    }

    /**
     * @return BelongsTo
     */
    public function userInvited()
    {
        return $this->belongsTo('Modules\Core\Entities\User', 'user_invited');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User', 'id', 'invite');
    }

    /**
     * @return BelongsTo
     */
    public function invitation()
    {
        return $this->belongsTo('Modules\Core\Entities\Invitation');
    }
}
