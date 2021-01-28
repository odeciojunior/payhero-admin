<?php

namespace Modules\Core\Entities;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class PendingDebt extends Model
{
    use LogsActivity;

    protected $keyType = 'integer';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'value',
        'reason',
        'sale_id',
        'withdrawal_id'
    ];


    protected static bool $logFillable = true;

    protected static bool $logUnguarded = true;

    protected static bool $logOnlyDirty = true;

    protected static bool $submitEmptyLogs = false;

    public function tapActivity(Activity $activity, string $eventName = '')
    {
        if ($eventName == 'deleted') {
            $activity->description = 'Débito foi deletedo.';
        } elseif ($eventName == 'updated') {
            $activity->description = 'Débito foi atualizado.';
        } elseif ($eventName == 'created') {
            $activity->description = 'Débito foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }
}
