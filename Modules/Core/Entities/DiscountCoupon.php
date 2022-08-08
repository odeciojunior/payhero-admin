<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\DiscountCouponsPresenter;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property int $project_id
 * @property string $name
 * @property boolean $type
 * @property string $value
 * @property string $code
 * @property boolean $status
 * @property mixed $progressive_rules
 * @property mixed $plans
 * @property boolean $discount
 * @property datetime $expires
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 */
class DiscountCoupon extends Model
{
    use SoftDeletes, PresentableTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = DiscountCouponsPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "project_id",
        "name",
        "type",
        "value",
        "code",
        "status",
        "progressive_rules",
        "plans",
        "discount",
        "expires",
        "rule_value",
        "created_at",
        "updated_at",
        "deleted_at",
    ];
    /**
     * @var bool
     */
    protected static $logFillable = true;
    /**
     * @var bool
     */
    protected static $logUnguarded = true;
    /**
     * Registra apenas os atributos alterados no log
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == "deleted") {
            $activity->description = "Cupom " . $this->name . " foi deletedo.";
        } elseif ($eventName == "updated") {
            $activity->description = "Cupom " . $this->name . " foi atualizado.";
        } elseif ($eventName == "created") {
            $activity->description = "Cupom " . $this->name . " foi criado.";
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo("Modules\Core\Entities\Project");
    }
}
