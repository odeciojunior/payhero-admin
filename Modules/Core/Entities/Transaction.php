<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Presenters\TransactionPresenter;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $sale_id
 * @property integer $company_id
 * @property string $value
 * @property integer $type
 * @property string $status
 * @property string $release_date
 * @property string $created_at
 * @property string $updated_at
 * @property string $antecipation_date
 * @property int $antecipable_value
 * @property int $antecipable_tax
 * @property string $currency
 * @property string $percentage_rate
 * @property string $transaction_rate
 * @property string $percentage_antecipable
 * @property Company $company
 * @property Sale $sale
 * @property AntecipatedTransaction[] $antecipatedTransactions
 * @property Transfer[] $transfers
 */
class Transaction extends Model
{
    use SoftDeletes, PresentableTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = TransactionPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'company_id',
        'invitation_id',
        'value',
        'type',
        'status',
        'status_enum',
        'release_date',
        'antecipation_date',
        'antecipable_value',
        'antecipable_tax',
        'currency',
        'percentage_rate',
        'transaction_rate',
        'percentage_antecipable',
        'installment_tax',
        'created_at',
        'updated_at',
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
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('Modules\Core\Entities\Company');
    }

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('Modules\Core\Entities\Sale');
    }

    /**
     * @return HasMany
     */
    public function antecipatedTransactions()
    {
        return $this->hasMany('Modules\Core\Entities\AntecipatedTransaction');
    }

    /**
     * @return HasMany
     */
    public function transfers()
    {
        return $this->hasMany('Modules\Core\Entities\Transfer');
    }

    /**
     * @return BelongsTo
     */
    public function invitation()
    {
        return $this->belongsTo('Modules\Core\Entities\Invitation');
    }
}
