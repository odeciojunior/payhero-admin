<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsaasTransfer extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'withdrawal_id',
        'transfer_id',
        'transaction_id',
        'value',
        'status',
        'sent_data',
        'response',
        'is_cloudfox',
        'created_at',
        'updated_at',        
    ];

    /**
     * @return BelongsTo
     */
    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class);
    }

    /**
     * @return BelongsTo
     */
    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }    
}

