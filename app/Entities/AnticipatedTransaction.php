<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnticipatedTransaction extends Model
{
    protected $table = 'antecipated_transactions';
    /**
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'tax',
        'tax_value',
        'days_to_release',
        'anticipation_id',
        'transaction_id',
    ];

    /**
     * @return BelongsTo
     */
    public function anticipation()
    {
        return $this->belongsTo('App\Entities\Anticipation');
    }

    /**
     * @return BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo('App\Entities\Transactions');
    }
}
