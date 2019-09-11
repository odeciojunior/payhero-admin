<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $sale_id
 * @property int $company_id
 * @property string $value
 * @property string $type
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
    use SoftDeletes;
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
        'value',
        'type',
        'status',
        'release_date',
        'created_at',
        'updated_at',
        'antecipation_date',
        'antecipable_value',
        'antecipable_tax',
        'currency',
        'percentage_rate',
        'transaction_rate',
        'percentage_antecipable',
    ];

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
}
