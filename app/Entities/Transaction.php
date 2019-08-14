<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $id
 * @property integer $sale
 * @property int $company
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
 * @property Transfer[] $transfers
 */
class Transaction extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'sale',
        'company',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Entities\Company', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('App\Entities\Sale', 'sale');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfers()
    {
        return $this->hasMany('App\Entities\Transfer', 'transaction');
    }

    /**
     * @return BelongsToMany
     */
    public function anticipations()
    {
        return $this->belongsToMany('App\Entities\Transaction', 'antecipated_transactions', 'transaction_id', 'anticipation_id');
    }
}
