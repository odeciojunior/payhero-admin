<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ActiveCampaignEventPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property integer $activecampaign_integration_id
 * @property int $event_sale
 * @property string $add_tags
 * @property string $remove_tags
 * @property int $remove_list
 * @property int $add_list
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property ActivecampaignIntegration $activecampaignIntegration
 * @property Plan $plan
 * @property Product $product
 */
class ActivecampaignEvent extends Model
{
    use SoftDeletes, PresentableTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = ActiveCampaignEventPresenter::class;
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
        "event_sale",
        "add_tags",
        "remove_tags",
        "remove_list",
        "add_list",
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

    /**
     * @return BelongsTo
     */
    public function activecampaignIntegration()
    {
        return $this->belongsTo("Modules\Core\Entities\ActivecampaignIntegration");
    }
}
