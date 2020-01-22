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
 * @property Project project
 */
class ProjectNotification extends Model
{
    use PresentableTrait, FoxModelTrait, LogsActivity;
    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    /**
     * @var array
     */
    protected $appends = ['id_code'];
    /**
     * @var string
     */
    protected $presenter = ProjectNotificationPresenter::class;
    /**
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'project_id',
        'status',
        'type_enum',
        'event_enum',
        'time',
        'message',
        'created_at',
        'updated_at',
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
            $activity->description = 'Notificação do projeto ' . $this->name . ' foi deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Notificação do projeto ' . $this->name . ' foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Notificação do projeto ' . $this->name . ' foi criado.';
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
        return $this->hasMany(UserProject::class, 'project_id', 'project_id');
    }
}
