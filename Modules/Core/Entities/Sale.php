<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\SalePresenter;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property int $owner_id
 * @property integer $affiliate_id
 * @property integer $client_id
 * @property integer $delivery_id
 * @property integer $shipping_id
 * @property int $project_id
 * @property integer $checkout_id
 * @property string $payment_form
 * @property int $payment_method
 * @property float $total_paid_value
 * @property float $shipment_value
 * @property string $start_date
 * @property string $end_date
 * @property string $gateway_id
 * @property int $status
 * @property string $gateway_status
 * @property int $installments_amount
 * @property string $installments_value
 * @property string $flag
 * @property string $boleto_link
 * @property string $boleto_digitable_line
 * @property string $boleto_due_date
 * @property string $cupom_code
 * @property string $shopify_order
 * @property string $iof
 * @property string $shopify_discount
 * @property string $dolar_quotation
 * @property boolean $first_confirmation
 * @property string $created_at
 * @property string $deleted_at
 * @property string $updated_at
 * @property Checkout $checkout
 * @property Project $project
 * @property Shipping $shipping
 * @property Affiliate $affiliate
 * @property Client $client
 * @property Delivery $delivery
 * @property User $user
 * @property PlansSale[] $plansSales
 * @property Transaction[] $transactions
 */
class Sale extends Model
{
    use FoxModelTrait, SoftDeletes, PresentableTrait;
    /**
     * @var string
     */
    protected $presenter = SalePresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'owner_id',
        'affiliate_id',
        'client_id',
        'delivery_id',
        'shipping_id',
        'project_id',
        'checkout_id',
        'payment_form',
        'payment_method',
        'total_paid_value',
        'shipment_value',
        'start_date',
        'end_date',
        'gateway_id',
        'status',
        'gateway_status',
        'installments_amount',
        'installments_value',
        'flag',
        'boleto_link',
        'boleto_digitable_line',
        'boleto_due_date',
        'cupom_code',
        'shopify_order',
        'iof',
        'shopify_discount',
        'dolar_quotation',
        'first_confirmation',
        'created_at',
        'deleted_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function checkout()
    {
        return $this->belongsTo('Modules\Core\Entities\Checkout');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Modules\Core\Entities\Project', 'project_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shipping()
    {
        return $this->belongsTo('Modules\Core\Entities\Shipping');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function affiliate()
    {
        return $this->belongsTo('Modules\Core\Entities\Affiliate');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('Modules\Core\Entities\Client');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery()
    {
        return $this->belongsTo('Modules\Core\Entities\Delivery');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User', 'owner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plansSales()
    {
        return $this->hasMany('Modules\Core\Entities\PlanSale');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany('Modules\Core\Entities\Transaction');
    }
}
