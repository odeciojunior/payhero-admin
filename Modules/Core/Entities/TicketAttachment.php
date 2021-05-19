<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property integer $ticket_id
 * @property string $file
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Ticket $ticket
 */
class TicketAttachment extends Model
{
    use LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'ticket_id',
        'file',
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
            $activity->description = 'O anexo do chamado foi deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'O anexo do chamado foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Anexo do chamado criado.';
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
