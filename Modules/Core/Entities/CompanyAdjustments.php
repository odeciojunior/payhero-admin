<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $company_id
 * @property string $adjustment_id
 * @property string $adjustment_amount
 * @property string $transaction_sign
 * @property string $adjustment_type
 * @property string $adjustment_amount_total
 * @property string $adjustment_reason
 * @property string $date_adjustment
 * @property string $subseller_rate_closing_date
 * @property mixed $data
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Company $company
 */
class CompanyAdjustments extends Model
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
    protected $dates = [
        'date_adjustment',
        'subseller_rate_closing_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * @var array
     */
    protected $fillable = [
        'company_id',
        'adjustment_id',
        'adjustment_amount',
        'transaction_sign',
        'adjustment_type',
        'adjustment_amount_total',
        'adjustment_reason',
        'date_adjustment',
        'subseller_rate_closing_date',
        'data',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('Modules\Core\Entities\Company');
    }
}
