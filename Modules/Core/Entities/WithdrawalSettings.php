<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\WithdrawalPresenter;
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

    /**
     * @var bool
     */
    protected static $logFillable = true;

    /**
     * @var bool
     */
    protected static $logUnguarded = true;

    /**
     * Registra apenas os atributos alterados
     * @var bool
     */
    protected static $logOnlyDirty = true;

    /**
     * Impede que o pacote armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == "deleted") {
            $activity->description = "Configurações de saque automático foi deletado.";
        } elseif ($eventName == "updated") {
            $activity->description = "Configurações de saque automático foi atualizado.";
        } elseif ($eventName == "created") {
            $activity->description = "Configurações de saque automático foi criado.";
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
