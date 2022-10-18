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
        "vega_available_balance",
        "vega_pending_balance",
        "vega_blocked_balance",
        "vega_total_balance",
        "asaas_available_balance",
        "asaas_pending_balance",
        "asaas_blocked_balance",
        "asaas_total_balance",
        "cielo_available_balance",
        "cielo_pending_balance",
        "cielo_blocked_balance",
        "cielo_total_balance",
        "getnet_available_balance",
        "getnet_pending_balance",
        "getnet_blocked_balance",
        "getnet_total_balance",
        "gerencianet_available_balance",
        "gerencianet_pending_balance",
        "gerencianet_blocked_balance",
        "gerencianet_total_balance",
        "total_balance",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\Company");
    }
}
