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
class CompanyBalanceLog extends Model
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "company_balance_logs";
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "company_id",
        "today_balance",
        "pending_balance",
        "available_balance",
        "total_balance",
        "created_at",
        "updated_at",
    ];

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
