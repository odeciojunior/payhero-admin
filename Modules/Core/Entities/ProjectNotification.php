<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ProjectNotificationPresenter;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $project_id
 * @property boolean $status
 * @property tinyint $type_enum
 * @property tinyint $event_enum
 * @property string $time
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project project
 * @method ProjectNotificationPresenter present()
 */
class ProjectNotification extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;

    public const STATUS_ACTIVE = 1;
    public const STATUS_DISABLED = 2;

    public const NOTIFICATION_SMS_BOLETO_GENERATED_IMMEDIATE = 1;
    public const NOTIFICATION_SMS_BOLETO_DUE_TODAY = 2;
    public const NOTIFICATION_SMS_ABANDONED_CART_AN_HOUR_LATER = 3;
    public const NOTIFICATION_SMS_ABANDONED_CART_NEXT_DAY = 4;
    public const NOTIFICATION_EMAIL_BOLETO_GENERATED_IMMEDIATE = 5;
    public const NOTIFICATION_EMAIL_BOLETO_GENERATED_NEXT_DAY = 6;
    public const NOTIFICATION_EMAIL_BOLETO_GENERATED_TWO_DAYS_LATER = 7;
    public const NOTIFICATION_EMAIL_BOLETO_DUE_TODAY = 8;
    public const NOTIFICATION_EMAIL_ABANDONED_CART_AN_HOUR_LATER = 9;
    public const NOTIFICATION_EMAIL_ABANDONED_CART_NEXT_DAY = 10;
    public const NOTIFICATION_SMS_CREDIT_CARD_PAID_IMMEDIATE = 11;
    public const NOTIFICATION_EMAIL_CREDIT_CARD_PAID_IMMEDIATE = 12;
    public const NOTIFICATION_EMAIL_BOLETO_PAID_IMMEDIATE = 13;
    public const NOTIFICATION_EMAIL_TRACKING_IMMEDIATE = 14;
    public const NOTIFICATION_SMS_TRACKING_IMMEDIATE = 15;
    public const NOTIFICATION_EMAIL_PIX_GENERATED_IMMEDIATE = 16;
    public const NOTIFICATION_EMAIL_PIX_PAID_IMMEDIATE = 17;
    public const NOTIFICATION_EMAIL_PIX_EXPIRED_AN_HOUR_LATER = 18;
    /**
     * @var array
     */
    protected $dates = ["created_at", "updated_at", "deleted_at"];
    /**
     * @var array
     */
    protected $appends = ["id_code"];
    /**
     * @var string
     */
    protected $presenter = ProjectNotificationPresenter::class;
    /**
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "project_id",
        "status",
        "type_enum",
        "event_enum",
        "notification_enum",
        "time",
        "message",
        "created_at",
        "updated_at",
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
        if ($eventName == "deleted") {
            $activity->description = "Notificação do projeto " . $this->name . " foi deletedo.";
        } elseif ($eventName == "updated") {
            $activity->description = "Notificação do projeto " . $this->name . " foi atualizado.";
        } elseif ($eventName == "created") {
            $activity->description = "Notificação do projeto " . $this->name . " foi criado.";
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return HasManyThrough
     */
    public function userProject()
    {
        return $this->hasMany(UserProject::class, "project_id", "project_id");
    }
}
