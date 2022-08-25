<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $activecampaign_integration_id
 * @property string $custom_field
 * @property int $custom_field_id
 * @property string $created_at
 * @property string $updated_at
 * @property ActivecampaignIntegration $activecampaignIntegration
 */
class ActivecampaignCustom extends Model
{
    use LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "activecampaign_integration_id",
        "custom_field",
        "custom_field_id",
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
    public function activecampaignIntegration()
    {
        return $this->belongsTo("Modules\Core\Entities\ActivecampaignIntegration");
    }
}
