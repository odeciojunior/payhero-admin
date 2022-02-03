<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\GatewayPresenter;
use App\Traits\LogsActivity;
use LogicException;
use Modules\Core\Interfaces\Statement;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;

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

    public const PAGARME_PRODUCTION_ID = 1;
    public const PAGARME_SANDBOX_ID = 2;
    public const ZOOP_PRODUCTION_ID = 3;
    public const ZOOP_SANDBOX_ID = 4;
    public const CIELO_PRODUCTION_ID = 5;
    public const CIELO_SANDBOX_ID = 6;
    public const GETNET_PRODUCTION_ID = 15;
    public const GETNET_SANDBOX_ID = 14;
    public const GERENCIANET_PRODUCTION_ID = 18;
    public const GERENCIANET_SANDBOX_ID = 19;
    public const ASAAS_PRODUCTION_ID = 8;
    public const ASAAS_SANDBOX_ID = 20;
    public const SAFE2PAY_PRODUCTION_ID = 21;
    public const SAFE2PAY_SANDBOX_ID = 22;

    public const PAYMENT_STATUS_CONFIRMED = 'CONFIRMED';

    /**
     * @var string
     */
    protected $presenter = GatewayPresenter::class;
    /**
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable = [
        'gateway_enum',
        'name',
        'json_config',
        'production_flag',
        'enabled_flag',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
    /**
     * @var bool
     */
    protected static $logFillable = true;
    /**
     * @var bool
     */
    protected static $logUnguarded = true;
    /**
     * Registra apenas os atributos alterados no log
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

    public function getService() : Statement
    {
        if(str_contains($this->name, 'getnet')) {
            return new GetnetService();
        }
        elseif(str_contains($this->name, 'gerencianet')) {
            return new GerencianetService();
        }
        elseif(str_contains($this->name, 'asaas')) {
            return new AsaasService();
        }
        elseif(str_contains($this->name, 'cielo')) {
            return new CieloService();
        }
        elseif(str_contains($this->name, 'safe2pay')) {
            return new CieloService();
        }
        else {
            throw new LogicException("Gateway {$this->name} não encontrado");
        }
    }
    
    public function getServiceById($gatewayId){
        if(!empty($gatewayId)){
            switch($gatewayId){
                case self::ASAAS_PRODUCTION_ID:
                    case self::ASAAS_SANDBOX_ID:
                        return new AsaasService();
                case self::GERENCIANET_PRODUCTION_ID:
                    case self::GERENCIANET_SANDBOX_ID:
                        return new GerencianetService();
                case self::GETNET_PRODUCTION_ID:
                    case self::GETNET_SANDBOX_ID:
                        return new GetnetService();
                case self::CIELO_PRODUCTION_ID:
                    case self::CIELO_SANDBOX_ID:
                        return new CieloService();
                default:
                    throw new LogicException("Gateway {self->name} não encontrado");
                break;
            }
        }             
    }

    /**
     * @return HasMany
     */
    public function gatewayFlags()
    {
        return $this->hasMany('Modules\Core\Entities\GatewayFlag');
    }

    /**
     * @return HasMany
     */
    public function gatewayPostbacks()
    {
        return $this->hasMany('Modules\Core\Entities\GatewayPostback');
    }

    /**
     * @return HasMany
     */
    public function saleGatewayRequests()
    {
        return $this->hasMany('Modules\Core\Entities\SaleGatewayRequest');
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany('Modules\Core\Entities\Sale');
    }
}
