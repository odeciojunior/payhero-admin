<?php

namespace Modules\Core\Entities;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\WithdrawalPresenter;
use Spatie\Activitylog\Models\Activity;

/**
 * @property int $id
 * @property int $company_id
 * @property string $value
 * @property string $release_date
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $bank
 * @property string $agency
 * @property string $agency_digit
 * @property string $account
 * @property string $account_digit
 * @property string $file
 * @property string $automatic_liquidation
 * @property Company $company
 */
class Withdrawal extends Model
{
    use LogsActivity, PresentableTrait, SoftDeletes;

    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_TRANSFERRED = 3;
    const STATUS_REFUSED = 4;
    const STATUS_IN_REVIEW = 5;
    const STATUS_PROCESSING = 6;
    const STATUS_RETURNED = 7;
    const STATUS_LIQUIDATING = 8;
    const STATUS_PARTIALLY_LIQUIDATED = 9;

    protected $presenter = WithdrawalPresenter::class;

    protected $keyType = 'integer';

    protected $dates = [
        'release_date',
        'created_at',
        'updated_at',
        'deleted_at',
        'release_date_new'
    ];

    protected $fillable = [
        'company_id',
        'gateway_id',
        'gateway_transfer_id',
        'value',
        'release_date',
        'status',
        'currency',
        'currency_quotation',
        'value_transferred',
        'tax',
        'bank',
        'agency',
        'agency_digit',
        'account',
        'account_digit',
        'release_date_new',
        'file',
        'observation',
        'automatic_liquidation',
        'is_released',
        'debt_pending_value',
        'created_at',
        'updated_at',
        'deleted_at',
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
        if ($eventName == 'deleted') {
            $activity->description = 'Pedido saque foi deletedo.';
        } elseif ($eventName == 'updated') {
            $activity->description = 'Pedido saque foi atualizado.';
        } elseif ($eventName == 'created') {
            $activity->description = 'Pedido saque foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Company');
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Gateway');
    }

    public function pendingDebts(): BelongsToMany
    {
        return $this->belongsToMany(PendingDebt::class, 'pending_debt_withdrawals');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
