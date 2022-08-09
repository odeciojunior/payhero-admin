<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\SalePresenter;
use Spatie\Activitylog\Models\Activity;

/**
 * Modules\Core\Entities\SaleRefundHistory
 *
 * @property int $id
 * @property int $sale_id
 * @property int|null $user_id
 * @property int $refunded_amount
 * @property string $date_refunded
 * @property mixed $gateway_response
 * @property int $refund_value
 * @property string|null $refund_observation
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read string $id_code
 * @property-read Sale $sale
 * @mixin \Eloquent
 */
class SaleRefundHistory extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = SalePresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "sale_id",
        "refunded_amount",
        "date_refunded",
        "gateway_response",
        "refund_value",
        "user_id",
        "refund_observation",
        "created_at",
        "deleted_at",
        "updated_at",
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

    public function sale(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\Sale");
    }
}
