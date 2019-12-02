<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\GatewayPresenter;

/**
 * Class Gateway
 * @package App\Entities
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
        'created_at',
        'updated_at',
    ];
}
