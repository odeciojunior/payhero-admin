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
 * @property string $filename
 * @property integer $type_enum
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Ticket $ticket
 */
class TicketAttachment extends Model
{
    use LogsActivity;

    const TYPE_FROM_CUSTOMER = 1;
    const TYPE_FROM_ADMIN = 2;
    const TYPE_FROM_SYSTEM = 3;

    protected $keyType = 'integer';

    protected $fillable = [
        'ticket_id',
        'file',
        'filename',
        'type_enum',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static $logFillable = true;

    protected static $logUnguarded = true;

    protected static $logOnlyDirty = true;

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
