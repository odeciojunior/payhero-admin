<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyBalance extends Model
{
    use SoftDeletes;

    protected $presenter = CompanyBalancePresenter::class;

    protected $fillable = [
        "company_id",
        "safe_2_pay_available_balance",
        "safe_2_pay_pending_balance",
        "asaas_available_balance",
        "asaas_pending_balance",
        "cielo_available_balance",
        "cielo_pending_balance",
        "getnet_available_balance",
        "getnet_pending_balance",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\Company");
    }
}
