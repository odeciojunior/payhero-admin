<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use DateTime;
use Gateway;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\SalePresenter;
use App\Traits\LogsActivity;

/**
 * @property integer $id
 * @property int $owner_id
 * @property integer $affiliate_id
 * @property integer $customer_id
 * @property integer $delivery_id
 * @property integer $shipping_id
 * @property int $project_id
 * @property integer $checkout_id
 * @property string $payment_form
 * @property int $payment_method
 * @property float $total_paid_value
 * @property float $shipment_value
 * @property string $start_date
 * @property DateTime $date_refunded
 * @property string $end_date
 * @property string $gateway_transaction_id
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
 * @property int $attempts
 * @property string gateway_card_flag
 * @property float gateway_tax_percent
 * @property integer gateway_tax_value
 * @property Checkout $checkout
 * @property Project $project
 * @property Shipping $shipping
 * @property Affiliate $affiliate
 * @property Customer $client
 * @property Delivery $delivery
 * @property SaleRefundHistory $saleRefundHistory
 * @property User $user
 * @property PlanSale[] $plansSales
 * @property Transaction[] $transactions
 * @property Tracking $tracking
 * @method SalePresenter present()
 */
class Sale extends Model
{
    use FoxModelTrait, SoftDeletes, PresentableTrait, LogsActivity;
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
        'customer_id',
        'delivery_id',
        'shipping_id',
        'project_id',
        'checkout_id',
        'payment_form',
        'payment_method',
        'total_paid_value',
        'sub_total',
        'shipment_value',
        'start_date',
        'date_refunded',
        'end_date',
        'gateway_transaction_id',
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
        'installment_tax_value',
        'attempts',
        'created_at',
        'deleted_at',
        'updated_at',
        'gateway_card_flag',
        'gateway_tax_percent',
        'gateway_tax_value',
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

    /**
     * @return BelongsTo
     */
    public function checkout()
    {
        return $this->belongsTo('Modules\Core\Entities\Checkout');
    }

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Modules\Core\Entities\Project', 'project_id');
    }

    /**
     * @return BelongsTo
     */
    public function shipping()
    {
        return $this->belongsTo('Modules\Core\Entities\Shipping');
    }

    /**
     * @return BelongsTo
     */
    public function affiliate()
    {
        return $this->belongsTo('Modules\Core\Entities\Affiliate');
    }

    /**
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Core\Entities\Customer');
    }

    /**
     * @return BelongsTo
     */
    public function delivery()
    {
        return $this->belongsTo('Modules\Core\Entities\Delivery');
    }

    /**
     * @return hasMany
     */
    public function saleRefundHistory()
    {
        return $this->hasMany('Modules\Core\Entities\SaleRefundHistory');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User', 'owner_id');
    }

    /**
     * @return HasMany
     */
    public function plansSales()
    {
        return $this->hasMany('Modules\Core\Entities\PlanSale');
    }

    /**
     * @return HasMany
     */
    public function transactions()
    {
        return $this->hasMany('Modules\Core\Entities\Transaction');
    }

    /**
     * @return HasMany
     */
    public function notazzInvoices()
    {
        return $this->hasMany('Modules\Core\Entities\NotazzInvoice');
    }

    /**
     * @return HasMany
     */
    public function productsPlansSale()
    {
        return $this->hasMany('Modules\Core\Entities\ProductPlanSale');
    }

    /**
     * @return BelongsTo
     */
    public function gateway()
    {
        return $this->belongsTo('Modules\Core\Entities\Gateway');
    }

    /**
     * @return HasMany
     */
    public function tracking()
    {
        return $this->hasMany(Tracking::class);
    }
}
