<?php


namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string name
 * @property string api
 * @property integer antifraud_api_enum,
 * @property string environment
 * @property string client_id
 * @property string client_secret
 * @property string merchant_id
 * @property boolean available_flag
 */
class Antifraud extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'api',
        'antifraud_api_enum',
        'environment',
        'client_id',
        'client_secret',
        'merchant_id',
        'available_flag',
    ];

}