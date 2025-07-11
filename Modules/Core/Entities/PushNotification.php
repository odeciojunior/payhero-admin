<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\PushNotificationPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $sale_id
 * @property string $postback_data
 * @property string $description
 * @property boolean $processed_flag
 * @property boolean $postback_valid_flag
 * @property string $machine_result
 * @property string $updated_at
 * @property string $created_at
 * @property string $deleted_at
 */
class PushNotification extends Model
{
    use SoftDeletes, PresentableTrait, FoxModelTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = PushNotificationPresenter::class;
    /**
     * @var array
     */
    protected $fillable = [
        "sale_id",
        "user_id",
        "withdrawal_id",
        "postback_data",
        "onesignal_response",
        "processed_flag",
        "postback_valid_flag",
        "machine_result",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }
}
