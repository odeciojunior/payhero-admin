<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Modules\Core\Entities\Antifraud
 *
 * @property int $id
 * @property string $name
 * @property string $api
 * @property int $antifraud_api_enum
 * @property string $environment
 * @property string $client_id
 * @property string $client_secret
 * @property string $merchant_id
 * @property int $available_flag
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Antifraud newModelQuery()
 * @method static Builder|Antifraud newQuery()
 * @method static Builder|Antifraud query()
 * @method static Builder|Antifraud whereAntifraudApiEnum($value)
 * @method static Builder|Antifraud whereApi($value)
 * @method static Builder|Antifraud whereAvailableFlag($value)
 * @method static Builder|Antifraud whereClientId($value)
 * @method static Builder|Antifraud whereClientSecret($value)
 * @method static Builder|Antifraud whereCreatedAt($value)
 * @method static Builder|Antifraud whereEnvironment($value)
 * @method static Builder|Antifraud whereId($value)
 * @method static Builder|Antifraud whereMerchantId($value)
 * @method static Builder|Antifraud whereName($value)
 * @method static Builder|Antifraud whereUpdatedAt($value)
 */
class Antifraud extends Model
{
    protected $keyType = "integer";

    protected $fillable = [
        "name",
        "api",
        "antifraud_api_enum",
        "environment",
        "client_id",
        "client_secret",
        "merchant_id",
        "available_flag",
    ];
}
