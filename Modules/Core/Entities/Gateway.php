<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\GatewayPresenter;
use Spatie\Activitylog\Traits\LogsActivity;
use LogicException;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Core\Services\Gateways\IuguService;
use Modules\Core\Services\Gateways\Safe2PayService;
use Modules\Core\Services\Gateways\VegaService;
use Spatie\Activitylog\LogOptions;

/**
 * Class Gateway
 * @package App\Entities
 * /**
 * @property integer $id
 * @property boolean $gateway_enum
 * @property string $name
 * @property string $json_config
 * @property boolean $production_flag
 * @property boolean $enabled_flag
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property GatewayFlag[] $gatewayFlags
 * @property GatewayPostback[] $gatewayPostbacks
 * @property SaleGatewayRequest[] $saleGatewayRequests
 * @property Sale[] $sales
 */
class Gateway extends Model
{
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;

    public const VEGA_PRODUCTION_ID = 1;
    public const VEGA_SANDBOX_ID = 2;

    public const SAFE2PAY_PRODUCTION_ID = 3;
    public const SAFE2PAY_SANDBOX_ID = 4;

    public const STARKBANK_PRODUCTION_ID = 5;
    public const STARKBANK_SANDBOX_ID = 6;

    public const IUGU_PRODUCTION_ID = 7;
    public const IUGU_SANDBOX_ID = 8;

    // disabled

    public const PAYMENT_STATUS_CONFIRMED = "CONFIRMED";

    /**
     * @var string
     */
    protected $presenter = GatewayPresenter::class;
    /**
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $dates = ["deleted_at"];
    /**
     * @var array
     */
    protected $fillable = [
        "gateway_enum",
        "name",
        "json_config",
        "production_flag",
        "enabled_flag",
        "deleted_at",
        "created_at",
        "updated_at",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    public static function getServiceById($gatewayId)
    {
        switch ($gatewayId) {
            case self::SAFE2PAY_PRODUCTION_ID:
            case self::SAFE2PAY_SANDBOX_ID:
                return new Safe2PayService();

            case self::IUGU_PRODUCTION_ID:
            case self::IUGU_SANDBOX_ID:
                return new IuguService();

            case self::VEGA_PRODUCTION_ID:
            case self::VEGA_SANDBOX_ID:
                return new VegaService();

            default:
                throw new LogicException("Gateway {self->name} não encontrado");
                break;
        }
    }

    /**
     * @return HasMany
     */
    public function gatewayFlags()
    {
        return $this->hasMany("Modules\Core\Entities\GatewayFlag");
    }

    /**
     * @return HasMany
     */
    public function gatewayPostbacks()
    {
        return $this->hasMany("Modules\Core\Entities\GatewayPostback");
    }

    /**
     * @return HasMany
     */
    public function saleGatewayRequests()
    {
        return $this->hasMany("Modules\Core\Entities\SaleGatewayRequest");
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany("Modules\Core\Entities\Sale");
    }
}
