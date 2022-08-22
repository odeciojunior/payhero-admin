<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property integer $id
 * @property int $company_id
 * @property integer $sale_id
 * @property string $type
 * @property string $request_date
 * @property string $confirm_date
 * @property string $payment_date
 * @property string $reason
 * @property int $value
 * @property string $created_at
 * @property string $updated_at
 * @property Company $company
 * @property Sale $sale
 * @property Withdrawal[] $withdrawals
 */
class PendingDebt extends Model
{
    use LogsActivity;

    const REVERSED = "REVERSED";
    const ADJUSTMENT = "ADJUSTMENT";

    protected $keyType = "integer";

    /**
     * @var array
     */
    protected $fillable = [
        "company_id",
        "sale_id",
        "type",
        "request_date",
        "confirm_date",
        "payment_date",
        "reason",
        "value",
        "created_at",
        "updated_at",
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function withdrawals(): BelongsToMany
    {
        return $this->belongsToMany(Withdrawal::class, "pending_debt_withdrawals");
    }
}
