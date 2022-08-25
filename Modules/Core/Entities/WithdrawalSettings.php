<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\WithdrawalPresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

/**
 * @property int $id
 * @property int $company_id
 * @property string $rule
 * @property string $frequency
 * @property int $weekday
 * @property int $day
 * @property int $amount
 * @property string $created_at
 * @property string $updated_at
 * @property Company $company
 */
class WithdrawalSettings extends Model
{
    use LogsActivity, PresentableTrait, FoxModelTrait, SoftDeletes;

    const RULE_PERIOD = "period";
    const RULE_AMOUNT = "amount";

    const FREQUENCY_DAILY = "daily";
    const FREQUENCY_WEEKLY = "weekly";
    const FREQUENCY_MONTHLY = "monthly";

    protected $presenter = WithdrawalPresenter::class;

    protected $keyType = "integer";

    protected $dates = ["created_at", "updated_at", "deleted_at"];

    protected $fillable = ["company_id", "rule", "frequency", "weekday", "day", "amount", "created_at", "updated_at"];

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
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
