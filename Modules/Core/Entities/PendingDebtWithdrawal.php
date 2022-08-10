<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $pending_debt_id
 * @property integer $withdrawal_id
 * @property PendingDebt $pendingDebt
 * @property Withdrawal $withdrawal
 */
class PendingDebtWithdrawal extends Model
{
    protected $fillable = ["pending_debt_id", "withdrawal_id"];

    public $timestamps = false;

    public function pendingDebt(): BelongsTo
    {
        return $this->belongsTo(PendingDebt::class);
    }

    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(Withdrawal::class);
    }
}
