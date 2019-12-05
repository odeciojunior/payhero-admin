<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\GatewayPresenter;

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
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */

    use PresentableTrait, SoftDeletes;
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gatewayFlags()
    {
        return $this->hasMany('ModulesCoreEntities\GatewayFlag');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gatewayPostbacks()
    {
        return $this->hasMany('ModulesCoreEntities\GatewayPostback');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saleGatewayRequests()
    {
        return $this->hasMany('ModulesCoreEntities\SaleGatewayRequest');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany('ModulesCoreEntities\Sale');
    }
}
