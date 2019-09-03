<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property int $carrier_id
 * @property string $receiver_name
 * @property string $zip_code
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $neighborhood
 * @property string $street
 * @property string $number
 * @property string $complement
 * @property integer $id_order_carrier
 * @property string $status_carrier
 * @property string $tracking_code
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Carrier $carrier
 * @property Sale[] $sales
 * @property TrackingHistory[] $trackingHistories
 */
class Delivery extends Model
{

    use SoftDeletes;

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = [
        'carrier_id', 
        'receiver_name', 
        'zip_code', 
        'country', 
        'state', 
        'city', 
        'neighborhood', 
        'street', 
        'number', 
        'complement', 
        'id_order_carrier', 
        'status_carrier', 
        'tracking_code', 
        'type', 
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier()
    {
        return $this->belongsTo('App\Entities\Carrier');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany('App\Entities\Sale');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trackingHistories()
    {
        return $this->hasMany('App\Entities\TrackingHistory');
    }
}
