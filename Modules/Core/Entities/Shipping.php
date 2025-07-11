<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ShippingPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property int $project_id
 * @property string $name
 * @property string $information
 * @property string $value
 * @property string $type
 * @property string $zip_code_origin
 * @property integer|null $melhorenvio_integration_id
 * @property boolean $status
 * @property boolean $pre_selected
 * @property boolean $use_variants
 * @property string $apply_on_plans
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property Collection $sales
 * @property MelhorenvioIntegration $melhorenvioIntegration
 */
class Shipping extends Model
{
    use SoftDeletes, FoxModelTrait, PresentableTrait, LogsActivity;

    public const TYPE_STATIC_ENUM = 1;
    public const TYPE_SEDEX_ENUM = 2;
    public const TYPE_PAC_ENUM = 3;
    public const TYPE_MELHORENVIO_ENUM = 4;

    public const STATUS_ACTIVE = 1;
    public const STATUS_DISABLED = 0;

    protected $keyType = "integer";

    protected $appends = ["id_code"];

    protected $presenter = ShippingPresenter::class;

    protected $fillable = [
        "project_id",
        "name",
        "information",
        "value",
        "regions_values",
        "type",
        "type_enum",
        "zip_code_origin",
        "melhorenvio_integration_id",
        "status",
        "rule_value",
        "pre_selected",
        "use_variants",
        "apply_on_plans",
        "not_apply_on_plans",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    protected $attributes = [
        "apply_on_plans" => ["all"],
        "not_apply_on_plans" => [],
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function melhorenvioIntegration(): BelongsTo
    {
        return $this->belongsTo(MelhorenvioIntegration::class);
    }
}
