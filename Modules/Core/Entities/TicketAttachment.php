<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    const TYPE_FROM_CUSTOMER = 1;
    const TYPE_FROM_ADMIN = 2;
    const TYPE_FROM_SYSTEM = 3;

    protected $keyType = "integer";

    protected $fillable = ["ticket_id", "file", "filename", "type_enum", "created_at", "updated_at", "deleted_at"];

    /**
     * @return BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
