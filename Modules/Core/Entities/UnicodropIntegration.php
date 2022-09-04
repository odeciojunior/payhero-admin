<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ReportanaIntegrationPresenter;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property int $user_id
 * @property int $project_id
 * @property string $token
 * @property boolean $billet_generated
 * @property boolean $billet_paid
 * @property boolean $credit_card_refused
 * @property boolean $credit_card_paid
 * @property boolean $abandoned_cart
 * @property boolean $pix
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Project $project
 * @property User $user
 */
class UnicodropIntegration extends Model
{
    use SoftDeletes, LogsActivity, PresentableTrait, HasFactory;
    /**
     * @var string
     */
    protected $presenter = ReportanaIntegrationPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "user_id",
        "project_id",
        "token",
        "billet_generated",
        "billet_paid",
        "credit_card_refused",
        "credit_card_paid",
        "abandoned_cart",
        "pix",
        "deleted_at",
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
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
