<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property int $project_id
 * @property int $user_id
 * @property string $link
 * @property boolean $boleto_generated
 * @property boolean $boleto_paid
 * @property boolean $credit_card_refused
 * @property boolean $credit_card_paid
 * @property boolean $abandoned_cart
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property User $user
 */
class HotzappIntegration extends Model
{
    use SoftDeletes, LogsActivity, HasFactory;
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
        "user_id",
        "link",
        "boleto_generated",
        "boleto_paid",
        "credit_card_refused",
        "credit_card_paid",
        "abandoned_cart",
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
    public function project()
    {
        return $this->belongsTo("Modules\Core\Entities\Project");
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo("Modules\Core\Entities\User");
    }
}
