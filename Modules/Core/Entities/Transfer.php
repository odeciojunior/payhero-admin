<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TransferPresenter;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property integer $transaction_id
 * @property int $user_id
 * @property int $company_id
 * @property int $gateway_id
 * @property int $anticipation_id
 * @property string $value
 * @property string $type
 * @property int $type_enum
 * @property string $reason
 * @property boolean $is_refund_tax
 * @property string $created_at
 * @property string $updated_at
 * @property Anticipation $anticipation
 * @property Transaction $transaction
 * @property User $user
 * @property Company $company
 * @method TransferPresenter present()
 */
class Transfer extends Model
{
    use FoxModelTrait, PresentableTrait, LogsActivity, SoftDeletes;

    const TYPE_IN = 1;
    const TYPE_OUT = 2;

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var string
     */
    protected $presenter = TransferPresenter::class;
    /**
     * @var array
     */
    protected $dates = ["created_at", "updated_at"];
    /**
     * @var array
     */
    protected $fillable = [
        "transaction_id",
        "user_id",
        "company_id",
        "gateway_id",
        "customer_id",
        "anticipation_id",
        "value",
        "type",
        "type_enum",
        "reason",
        "is_refund_tax",
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
            $activity->description = "Extrato foi deletedo.";
        } elseif ($eventName == "updated") {
            $activity->description = "Extrato foi atualizado.";
        } elseif ($eventName == "created") {
            $activity->description = "Extrato foi criado.";
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function anticipation()
    {
        return $this->belongsTo("Modules\Core\Entities\Anticipation");
    }

    /**
     * @return BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo("Modules\Core\Entities\Transaction");
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo("Modules\Core\Entities\User");
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo("Modules\Core\Entities\Company");
    }

    /**
     * @return BelongsTo
     */
    public function gateway()
    {
        return $this->belongsTo("Modules\Core\Entities\Gateway");
    }

    /**
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo("Modules\Core\Entities\Customer");
    }

    public function asaasTransfer()
    {
        return $this->hasMany(AsaasTransfer::class);
    }
}
