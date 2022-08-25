<?php

namespace Modules\Core\Entities;

use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TicketMessagePresenter;
use Spatie\Activitylog\LogOptions;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
