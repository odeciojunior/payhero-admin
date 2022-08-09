<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modules\Core\Entities\SaleGatewayRequest
 *
 * @property int $id
 * @property int $sale_id
 * @property int|null $gateway_id
 * @property mixed|null $send_data
 * @property mixed|null $gateway_result
 * @property mixed|null $gateway_exceptions
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Sale $sale
 * @method static Builder|SaleGatewayRequest newModelQuery()
 * @method static Builder|SaleGatewayRequest newQuery()
 * @method static Builder|SaleGatewayRequest query()
 * @method static Builder|SaleGatewayRequest whereCreatedAt($value)
 * @method static Builder|SaleGatewayRequest whereGatewayExceptions($value)
 * @method static Builder|SaleGatewayRequest whereGatewayId($value)
 * @method static Builder|SaleGatewayRequest whereGatewayResult($value)
 * @method static Builder|SaleGatewayRequest whereId($value)
 * @method static Builder|SaleGatewayRequest whereSaleId($value)
 * @method static Builder|SaleGatewayRequest whereSendData($value)
 * @method static Builder|SaleGatewayRequest whereUpdatedAt($value)
 */
class SaleGatewayRequest extends Model
{
    public $fillable = ["id", "sale_id", "gateway_id", "send_data", "gateway_result", "gateway_exceptions"];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
