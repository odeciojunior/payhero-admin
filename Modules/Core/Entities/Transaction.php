<?php

namespace Modules\Core\Entities;

use Spatie\Activitylog\Traits\LogsActivity;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TransactionPresenter;
use Spatie\Activitylog\LogOptions;

/**
 * @property int $id
 * @property int $sale_id
 * @property int $gateway_id
 * @property int $company_id
 * @property int $user_id
 * @property int $withdrawal_id
 * @property int $invitation_id
 * @property string $value
 * @property int $type
 * @property string $status
 * @property int $status_enum
 * @property date $release_date
 * @property string $installment_tax
 * @property string $checkout_tax
 * @property string $tax
 * @property integer $tax_type
 * @property string $transaction_rate
 * @property int $tax_type
 * @property string $transaction_tax
 * @property string $gateway_released_at
 * @property boolean $gateway_transferred
 * @property datetime $gateway_transferred_at
 * @property boolean $is_waiting_withdrawal
 * @property boolean $tracking_required
 * @property boolean $is_security_reserve
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
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

    const TYPE_CLOUDFOX = 1;
    const TYPE_PRODUCER = 2;
    const TYPE_INVITATION = 3;
    const TYPE_AFFILIATE = 4;
    const TYPE_PARTNER = 5;
    const TYPE_CONVERTAX = 6;
    const TYPE_REFUNDED = 7;
    const TYPE_CLOUDFOX_PROCESSING = 8;

    const TYPE_PERCENTAGE_TAX = 1;
    const TYPE_VALUE_TAX = 2;

    const STATUS_TRANSFERRED = 1;
    const STATUS_PAID = 2;
    const STATUS_PENDING = 3;
    const STATUS_CHARGEBACK = 4;
    const STATUS_CANCELED = 5;
    const STATUS_REFUNDED = 6;
    const STATUS_REFUSED = 7;
    const STATUS_PENDING_ANTIFRAUD = 8;
    const STATUS_CANCELED_ANTIFRAUD = 9;
    const STATUS_IN_PROCESS = 10;
    //const STATUS_WAITING_WITHDRAWAL = 10;
    const STATUS_ANTICIPATED = 12;
    const STATUS_BILLET_REFUNDED = 13;

    protected $presenter = TransactionPresenter::class;

    protected $keyType = "integer";

    protected $dates = ["created_at", "updated_at", "deleted_at"];

    protected $fillable = [
        "sale_id",
        "gateway_id",
        "company_id",
        "user_id",
        "withdrawal_id",
        "invitation_id",
        "value",
        "type",
        "status",
        "status_enum",
        "release_date",
        "installment_tax",
        "checkout_tax",
        "tax",
        "tax_type",
        "transaction_tax",
        "gateway_released_at",
        "gateway_transferred",
        "gateway_transferred_at",
        "is_waiting_withdrawal",
        "tracking_required",
        "is_security_reserve",
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
        return $this->hasMany(ProductPlanSale::class, "sale_id", "sale_id");
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
        return $this->hasMany(BlockReasonSale::class, "sale_id", "sale_id");
    }
}
