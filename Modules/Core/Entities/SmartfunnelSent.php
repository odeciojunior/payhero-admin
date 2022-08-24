<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\SmartfunnelSentPresenter;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property integer $sale_id
 * @property integer $smartfunnel_integration_id
 * @property string $data
 * @property string $response
 * @property int $sent_status
 * @property string $created_at
 * @property string $updated_at
 * @property SmartfunnelIntegration $smartfunnelIntegration
 * @property Sale $sale
 */
class SmartfunnelSent extends Model
{
    use LogsActivity, PresentableTrait;
    /**
     * @var string
     */
    protected $presenter = SmartfunnelSentPresenter::class;
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "smartfunnel_sent";
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "sale_id",
        "smartfunnel_integration_id",
        "data",
        "response",
        "sent_status",
        "event_sale",
        "created_at",
        "updated_at",
    ];

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
    public function smartfunnelIntegration()
    {
        return $this->belongsTo(SmartfunnelIntegration::class);
    }

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
