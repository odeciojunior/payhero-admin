<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

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
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

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
        'percentage_antecipable'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Entities\Company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('App\Entities\Sale');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function antecipatedTransactions()
    {
        return $this->hasMany('App\Entities\AntecipatedTransaction');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfers()
    {
        return $this->hasMany('App\Entities\Transfer');
    }
}
