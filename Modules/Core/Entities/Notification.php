<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property integer $notifiable_id
 * @property string $data
 * @property string $read_at
 * @property string $created_at
 * @property string $updated_at
 */
class Notification extends Model
{
    use LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "string";
    /**
     * Indicates if the IDs are auto-incrementing.
     * @var bool
     */
    public $incrementing = false;
    /**
     * @var array
     */
    protected $fillable = ["type", "notifiable_type", "notifiable_id", "data", "read_at", "created_at", "updated_at"];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }
}
