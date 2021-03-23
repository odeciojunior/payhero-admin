<?php

namespace Modules\Core\Entities;

use App\Traits\LogsActivity;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TransactionPresenter;

/**
 * Modules\Core\Entities\Transaction
 *
 * @property integer $id
 * @property integer $sale_id
 * @property integer $gateway_id
 * @property integer $company_id
 * @property integer $user_id
 * @property string $value
 * @property integer $type
 * @property string $status
 * @property string $release_date
 * @property string $created_at
 * @property string $updated_at
 * @property string $percentage_rate
 * @property string $transaction_rate
 * @property string $gateway_released_at
 * @property boolean $is_waiting_withdrawal
 * @property integer $withdrawal_id
 * @property Company $company
 * @property Sale $sale
 * @property Gateway $gateway
 * @property Withdrawal $withdrawal
 * @property Collection $antecipatedTransactions
 * @property Collection $transfers
 * @method TransactionPresenter present()
 * @mixin Eloquent
 */
class Transaction extends Model
{
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;

    public const TYPE_CLOUDFOX = 1;
    public const TYPE_PRODUCER = 2;
    public const TYPE_INVITATION = 3;
    public const TYPE_AFFILIATE = 4;
    public const TYPE_PARTNER = 5;
    public const TYPE_CONVERTAX = 6;
    public const TYPE_REFUNDED = 7;

    public const STATUS_TRANSFERED = 1;
    public const STATUS_PAID = 2;
    public const STATUS_PENDING = 3;
    public const STATUS_CHARGEDBACK = 4;
    public const STATUS_CANCELED = 5;
    public const STATUS_REFUNDED = 6;
    public const STATUS_REFUSED = 7;
    public const STATUS_PENDING_ANTIFRAUD = 8;
    public const STATUS_CANCELED_ANTIFRAUD = 9;
    public const STATUS_WAITING_WITHDRAWAL = 10;
    public const STATUS_ANTICIPATED = 12;
    public const STATUS_BILLET_REFUNDED = 13;


    protected $presenter = TransactionPresenter::class;

    protected $keyType = 'integer';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'sale_id',
        'gateway_id',
        'company_id',
        'user_id',
        'invitation_id',
        'value',
        'type',
        'status',
        'status_enum',
        'release_date',
        'percentage_rate',
        'transaction_rate',
        'installment_tax',
        'created_at',
        'updated_at',
        'gateway_released_at',
        'is_waiting_withdrawal',
        'withdrawal_id',
        'gateway_transferred'
    ];

    protected static $logFillable = true;

    protected static $logUnguarded = true;

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function save(array $options = [])
    {
        $transaction = parent::save($options);
        if ($this->value < 0) {
            report(new Exception("Created Transaction valor negativo: {$this->id} "));
        }

        return $transaction;
    }

    public function update(array $attributes = [], array $options = [])
    {
        $transaction = parent::update($attributes, $options);
        if ($this->value < 0) {
            report(new Exception("Update Transaction valor negativo: {$this->id}"));
        }

        return $transaction;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function anticipatedTransactions(): HasMany
    {
        return $this->hasMany(AnticipatedTransaction::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function productPlanSales(): HasMany
    {
        return $this->hasMany(ProductPlanSale::class, 'sale_id', 'sale_id');
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }

    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(Withdrawal::class);
    }

    public function blockReasonSale(): HasMany
    {
        return $this->hasMany(BlockReasonSale::class, 'sale_id', 'sale_id');
    }
}
