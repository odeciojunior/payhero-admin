<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $checkout_id
 * @property string $id_log_session
 * @property string $plan
 * @property string $event
 * @property string $name
 * @property string $email
 * @property string $document
 * @property string $telephone
 * @property string $zip_code
 * @property string $street
 * @property string $number
 * @property string $neighborhood
 * @property string $city
 * @property string $state
 * @property string $total_value
 * @property string $error
 * @property string $created_at
 * @property string $updated_at
 * @property Checkout $checkout
 */

class Log extends Model
{

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['checkout_id', 'id_log_session', 'plan', 'event', 'name', 'email', 'document', 'telephone', 'zip_code', 'street', 'number', 'neighborhood', 'city', 'state', 'total_value', 'error', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function checkout()
    {
        return $this->belongsTo('Modules\Core\Entities\Checkout');
    }
}
