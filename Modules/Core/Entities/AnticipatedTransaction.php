<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $anticipation_id
 * @property integer $transaction_id
 * @property string $tax
 * @property string $tax_value
 * @property string $days_to_release
 * @property string $created_at
 * @property string $updated_at
 * @property Anticipation $anticipation
 * @property Transaction $transaction
 */
class AnticipatedTransaction extends Model
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
    protected $fillable = [
        'anticipation_id',
        'transaction_id',
        'tax',
        'tax_value',
        'days_to_release',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anticipation()
    {
        return $this->belongsTo('Modules\Core\Entities\Anticipation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo('Modules\Core\Entities\Transaction');
    }
}
