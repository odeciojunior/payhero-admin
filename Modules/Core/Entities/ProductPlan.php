<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ProductPlanPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property integer $product_id
 * @property integer $plan_id
 * @property int $amount
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Plan $plan
 * @property Product $product
 */
class ProductPlan extends Model
{
    use FoxModelTrait, SoftDeletes, PresentableTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = ProductPlanPresenter::class;
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "products_plans";
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "product_id",
        "plan_id",
        "amount",
        "cost",
        "is_custom",
        "custom_config",
        "currency_type_enum",
        "active_flag",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    protected $casts = [
        "custom_config" => "array",
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
    public function plan()
    {
        return $this->belongsTo("Modules\Core\Entities\Plan");
    }

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo("Modules\Core\Entities\Product");
    }
}
