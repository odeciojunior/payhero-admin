<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

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
    use SoftDeletes, LogsActivity;
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
    public function anticipation()
    {
        return $this->belongsTo('Modules\Core\Entities\Anticipation');
    }

    /**
     * @return BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo('Modules\Core\Entities\Transaction');
    }
}
