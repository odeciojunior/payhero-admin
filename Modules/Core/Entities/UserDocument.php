<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\UserDocumentPresenter;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property int $user_id
 * @property string $document_url
 * @property boolean $document_type_enum
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class UserDocument extends Model
{
    use PresentableTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = UserDocumentPresenter::class;
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
        'document_url',
        'document_type_enum',
        'status',
        'refused_reason',
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
            $activity->description = 'Documento do usuario foi deletado.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Documento do usuario foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Documento do usuario foi criado';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }
}
