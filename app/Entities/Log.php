<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $id_log_session
 * @property string $plan
 * @property string $event
 * @property string $user_agent
 * @property string $access_hour
 * @property string $horary
 * @property string $operational_system
 * @property string $browser
 * @property string $forward
 * @property string $reference
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
 * @property string $shipment_value
 * @property string $cupon_value
 * @property string $total_value
 * @property boolean $card_number
 * @property string $card_name
 * @property string $card_document
 * @property boolean $card_month
 * @property boolean $card_year
 * @property string $installments
 * @property string $error
 * @property string $created_at
 * @property string $updated_at
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
    protected $fillable = ['id_log_session', 'plan', 'event', 'user_agent', 'access_hour', 'horary', 'operational_system', 'browser', 'forward', 'reference', 'name', 'email', 'document', 'telephone', 'zip_code', 'street', 'number', 'neighborhood', 'city', 'state', 'shipment_value', 'cupon_value', 'total_value', 'card_number', 'card_name', 'card_document', 'card_month', 'card_year', 'installments', 'error', 'created_at', 'updated_at'];

}
