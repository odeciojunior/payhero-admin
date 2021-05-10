<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property int $company_id
 * @property int $value
 * @property int $tax
 * @property string $percentage_tax
 * @property string $percentage_anticipable
 * @property string $created_at
 * @property string $updated_at
 * @property Company $company
 * @property AnticipatedTransaction[] $anticipatedTransactions
 */
class Anticipation extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = ['company_id', 'value', 'tax', 'percentage_tax', 'percentage_anticipable', 'created_at', 'updated_at'];

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
    public function anticipatedTransactions()
    {
        return $this->hasMany('Modules\Core\Entities\AnticipatedTransaction');
    }

    /**
     * @return HasMany
     */
    public function transfers()
    {
        return $this->hasMany('Modules\Core\Entities\Transfer');
    }
}
