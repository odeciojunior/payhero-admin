<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

/**
 * @property integer $id
 * @property int $company_id
 * @property string $value
 * @property string $tax
 * @property string $percentage_tax
 * @property string $release_money_days
 * @property string $created_at
 * @property string $updated_at
 * @property Company $company
 * @property AntecipatedTransaction[] $antecipatedTransactions
 */
class Anticipation extends Model
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
        'company_id',
        'value',
        'tax',
        'percentage_tax',
        'release_money_days',
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
     * @return HasMany
     */
    public function antecipatedTransactions()
    {
        return $this->hasMany('Modules\Core\Entities\AntecipatedTransaction');
    }
}
