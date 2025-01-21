<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Company
 *
 * @package Modules\Core\Entities
 * @property int $company_id
 * @property int $gateway_id
 * @property int|null $gateway_status
 * @property string $gateway_subseller_id
 * @property string|null $gateway_api_key*
 * @property Company $company
 * @property Gateway[] $gateway
 */
class GatewaysCompaniesCredential extends Model
{
    public const GATEWAY_STATUS_APPROVED = 1;
    public const GATEWAY_STATUS_REVIEW = 2;
    public const GATEWAY_STATUS_REPROVED = 3;
    public const GATEWAY_STATUS_APPROVED_GETNET = 4;
    public const GATEWAY_STATUS_ERROR = 5;
    public const GATEWAY_STATUS_PENDING = 6;

    protected $fillable = [
        "company_id",
        "gateway_id",
        "gateway_status",
        "gateway_subseller_id",
        "gateway_api_key",
        "capture_transaction_enabled",
        "has_transfers_webhook",
        "has_charges_webhook",
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }
}
