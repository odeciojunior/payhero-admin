<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    use SoftDeletes;

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
        'company_id', 
        'value', 
        'tax', 
        'percentage_tax', 
        'release_money_days', 
        'created_at', 
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Entities\Company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function antecipatedTransactions()
    {
        return $this->hasMany('App\Entities\AntecipatedTransaction');
    }
}
