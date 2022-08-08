<?php

namespace Modules\Core\Entities;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TicketMessagePresenter;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property integer $ticket_id
 * @property string $message
 * @property integer $type_enum
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Ticket $ticket
 * @method TicketMessagePresenter present()
 */
class TicketMessage extends Model
{
    use PresentableTrait, LogsActivity;

    const TYPE_FROM_CUSTOMER = 1;
    const TYPE_FROM_ADMIN = 2;
    const TYPE_FROM_SYSTEM = 3;

    protected $presenter = TicketMessagePresenter::class;

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = ["ticket_id", "message", "type_enum", "created_at", "updated_at", "deleted_at"];
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
            $activity->description = "A mensagem do chamado foi deletedo.";
        } elseif ($eventName == "updated") {
            $activity->description = "A mensagem do chamado foi atualizado.";
        } elseif ($eventName == "created") {
            $activity->description = "Mensagem do chamado criada.";
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
