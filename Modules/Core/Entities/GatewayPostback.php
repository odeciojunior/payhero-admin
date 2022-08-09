<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class GatewayPostback extends Model
{
    use HasFactory;
    use PresentableTrait;
    use SoftDeletes;

    public const GATEWAY_PAGARME_ENUM = 1;
    public const GATEWAY_ZOOP_ENUM = 2;
    public const GATEWAY_CIELO_ENUM = 3;
    public const GATEWAY_PAGHIPER_ENUM = 4;
    public const GATEWAY_MERCADO_PAGO_ENUM = 5;
    public const GATEWAY_ASAAS_ENUM = 6;
    public const GATEWAY_JUNO_ENUM = 7;
    public const GATEWAY_GETNET_ENUM = 8;
    public const GATEWAY_BRASPAG_ENUM = 9;
    public const GATEWAY_GERENCIANET_ENUM = 10;

    protected $keyType = "integer";

    protected $dates = ["deleted_at"];

    protected $fillable = [
        "sale_id",
        "gateway_id",
        "reference_id",
        "data",
        "gateway_enum",
        "gateway_postback_type",
        "gateway_status",
        "gateway_payment_type",
        "description",
        "amount",
        "processed_flag",
        "postback_valid_flag",
        "pay_postback_flag",
        "machine_result",
        "created_at",
        "updated_at",
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
