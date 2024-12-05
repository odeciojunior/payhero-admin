<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use LogicException;
use Modules\Core\Presenters\GatewayPresenter;
use Modules\Core\Services\Gateways\AbmexService;
use Modules\Core\Services\Gateways\IuguService;
use Modules\Core\Services\Gateways\MalgaService;
use Modules\Core\Services\Gateways\PayupService;
use Modules\Core\Services\Gateways\Safe2PayService;
use Modules\Core\Services\Gateways\SimPayService;
use Modules\Core\Services\Gateways\ArmPayService;
use Modules\Core\Services\Gateways\AxisBankingService;
use Modules\Core\Services\Gateways\VegaService;
use Modules\Core\Services\Gateways\VolutiService;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

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

    public const SUITPAY_PRODUCTION_ID = 9;
    public const SUITPAY_SANDBOX_ID = 10;

    public const ABMEX_PRODUCTION_ID = 11;
    public const ABMEX_SANDBOX_ID = 12;

    public const SIMPAY_PRODUCTION_ID = 13;
    public const SIMPAY_SANDBOX_ID = 14;

    public const EFIPAY_PRODUCTION_ID = 15;
    public const EFIPAY_SANDBOX_ID = 16;

    public const PAYUP_PRODUCTION_ID = 17;
    public const PAYUP_SANDBOX_ID = 18;

    public const MALGA_PRODUCTION_ID = 19;
    public const MALGA_SANDBOX_ID = 20;

    public const ARMPAY_PRODUCTION_ID = 21;
    public const ARMPAY_SANDBOX_ID = 22;

    public const VOLUTI_PRODUCTION_ID = 23;
    public const VOLUTI_SANDBOX_ID = 24;

    public const AXISBANKING_PRODUCTION_ID = 25;
    public const AXISBANKING_SANDBOX_ID = 26;

    public const PAYMENT_STATUS_CONFIRMED = "CONFIRMED";

    public const ASAAS_PRODUCTION_ID = 999;
    public const ASAAS_SANDBOX_ID = 998;
    public const GETNET_PRODUCTION_ID = 997;
    public const GETNET_SANDBOX_ID = 996;
    public const GERENCIANET_PRODUCTION_ID = 995;
    public const GERENCIANET_SANDBOX_ID = 994;
    public const CIELO_PRODUCTION_ID = 993;
    public const CIELO_SANDBOX_ID = 992;

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

            case self::ABMEX_PRODUCTION_ID:
            case self::ABMEX_SANDBOX_ID:
                return new AbmexService();

            case self::SIMPAY_PRODUCTION_ID:
            case self::SIMPAY_SANDBOX_ID:
                return new SimPayService();

            case self::PAYUP_PRODUCTION_ID:
            case self::PAYUP_SANDBOX_ID:
                return new PayupService();

            case self::MALGA_PRODUCTION_ID:
            case self::MALGA_SANDBOX_ID:
                return new MalgaService();

            case self::ARMPAY_PRODUCTION_ID:
            case self::ARMPAY_SANDBOX_ID:
                return new ArmPayService();

            case self::VOLUTI_PRODUCTION_ID:
            case self::VOLUTI_SANDBOX_ID:
                return new VolutiService();
            
            case self::AXISBANKING_PRODUCTION_ID:
            case self::AXISBANKING_SANDBOX_ID:
                return new AxisBankingService();

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
