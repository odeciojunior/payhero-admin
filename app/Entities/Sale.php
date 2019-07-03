<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $owner
 * @property integer $affiliate
 * @property integer $client
 * @property integer $delivery
 * @property string $payment_form
 * @property float $total_paid_value
 * @property float $shipment_value
 * @property string $start_date
 * @property string $end_date
 * @property string $gateway_id
 * @property string $gateway_status
 * @property string $status
 * @property int $installments_amount
 * @property string $installments_value
 * @property string $flag
 * @property string $boleto_link
 * @property string $boleto_digitable_line
 * @property string $boleto_due_date
 * @property string $cupom_code
 * @property string $shopify_order
 * @property string $ip
 * @property string $dolar_quotation
 * @property string $created_at
 * @property string $deleted_at
 * @property string $updated_at
 * @property Affiliate $affiliate
 * @property Client $client
 * @property Delivery $delivery
 * @property User $user
 * @property PlansSale[] $plansSales
 * @property Transaction[] $transactions
 */
class Sale extends Model
{
    use FoxModelTrait;
    protected $dates = ['deleted_at'];
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'owner',
        'affiliate',
        'client',
        'delivery',
        'payment_form',
        'total_paid_value',
        'shipment_value',
        'start_date',
        'end_date',
        'gateway_id',
        'gateway_status',
        'status',
        'installments_amount',
        'installments_value',
        'flag',
        'boleto_link',
        'boleto_digitable_line',
        'boleto_due_date',
        'cupom_code',
        'shopify_order',
        'ip',
        'dolar_quotation',
        'project',
        'created_at',
        'deleted_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function affiliate()
    {
        return $this->belongsTo('App\Entities\Affiliate', 'affiliate');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function clientModel()
    {
        return $this->belongsTo('App\Entities\Client', 'client');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery()
    {
        return $this->belongsTo('App\Entities\Delivery', 'delivery');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Entities\User', 'owner');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plansSales()
    {
        return $this->hasMany('App\Entities\PlanSale', 'sale');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany('App\Entities\Transaction', 'sale');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectModel()
    {
        return $this->belongsTo('App\Entities\Project', 'project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shippingModel()
    {
        return $this->hasOne('App\Entities\Shipping', 'shipping');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function checkoutModel()
    {
        return $this->hasOne('App\Entities\Checkout', 'checkout');
    }
}
