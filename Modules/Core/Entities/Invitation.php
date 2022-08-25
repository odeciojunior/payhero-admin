<?php

namespace Modules\Core\Entities;

use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\InvitePresenter;
use Spatie\Activitylog\LogOptions;

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
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "invite",
        "user_invited",
        "company_id",
        "invitation_id",
        "email_invited",
        "status",
        "register_date",
        "expiration_date",
        "parameter",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, "company_id");
    }

    public function userInvited(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_invited");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "id", "invite");
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
