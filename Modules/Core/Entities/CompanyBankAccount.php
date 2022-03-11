<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $company_id
 * @property string $today_balance
 * @property string $pending_balance
 * @property string $available_balance
 * @property string $total_balance
 * @property string $created_at
 * @property string $updated_at
 * @property Sale $sale
 */
class CompanyBankAccount extends Model
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
        'company_id',
        'transfer_type',
        'type_key_pix',
        'key_pix',
        'bank',
        'agency',
        'agency_digit',
        'account',
        'account_digit',
        'is_default',
        'status',
        'gateway_transaction_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    } 
}