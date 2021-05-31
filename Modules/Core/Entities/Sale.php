<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\LogsActivity;
use Barryvdh\LaravelIdeHelper\Eloquent;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\SalePresenter;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Modules\Core\Entities\Sale
 *
 * @property integer $id
 * @property int $owner_id
 * @property int $company_id
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
 * @property string gateway_order_id
 * @property string $gateway_id
 * @property int $status
 * @property string $gateway_status
 * @property int $upsell_id
 * @property int $installments_amount
 * @property string $installments_value
 * @property string $flag
 * @property string $boleto_link
 * @property string $boleto_digitable_line
 * @property string $boleto_due_date
 * @property string $cupom_code
 * @property string $shopify_order
 * @property string $shopify_discount
 * @property string $dolar_quotation
 * @property boolean $first_confirmation
 * @property int $attempts
 * @property string $gateway_card_flag
 * @property float $gateway_tax_percent
 * @property integer $gateway_tax_value
 * @property boolean $has_valid_tracking
 * @property boolean $has_order_bump
 * @property string $observation
 * @property string $created_at
 * @property string $deleted_at
 * @property string $updated_at
 * @property Checkout $checkout
 * @property Project $project
 * @property Shipping $shipping
 * @property Affiliate $affiliate
 * @property Customer $customer
 * @property Delivery $delivery
 * @property SaleRefundHistory $saleRefundHistory
 * @property User $user
 * @property Company $company
 * @property Collection $plansSales
 * @property Collection $transactions
 * @property Collection $productsPlansSale
 * @property Tracking $tracking
 * @property Collection $upsells
 * @method SalePresenter present()
 * @mixin Eloquent
 */
class Sale extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;

    public const GETNET_SANDBOX_ID = 14;
    public const GETNET_PRODUCTION_ID = 15;

    public const CREDIT_CARD_PAYMENT = 1;
    public const BOLETO_PAYMENT = 2;
    public const PIX_PAYMENT = 4;

    public const STATUS_APPROVED = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_REFUSED = 3;
    public const STATUS_CHARGEBACK = 4;
    public const STATUS_CANCELED = 5;
    public const STATUS_IN_PROCESS = 6;
    public const STATUS_REFUNDED = 7;
    public const STATUS_PARTIAL_REFUNDED = 8;
    public const STATUS_BLACK_LIST = 10;
    public const STATUS_IN_REVIEW = 20;
    public const STATUS_CANCELED_ANTIFRAUD = 21;
    public const STATUS_BILLET_REFUNDED = 22;
    public const STATUS_IN_DISPUTE = 24;
    public const STATUS_IN_REVIEW_QUESTION = 30;
    public const STATUS_SYSTEM_ERROR = 99;

    const PAYMENT_TYPE_CREDIT_CARD = 1;
    const PAYMENT_TYPE_BANK_SLIP = 2;
    const PAYMENT_TYPE_DEBIT = 3;

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
        'company_id',
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
        'gateway_order_id',
        'gateway_billet_identificator',
        'gateway_id',
        'status',
        'gateway_status',
        'upsell_id',
        'installments_amount',
        'installments_value',
        'flag',
        'boleto_link',
        'boleto_digitable_line',
        'boleto_due_date',
        'cupom_code',
        'shopify_order',
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
        'automatic_discount',
        'interest_total_value',
        'refund_value',
        'is_chargeback',
        'is_chargeback_recovered',
        'has_valid_tracking',
        'has_order_bump',
        'observation',
        'original_total_paid_value'
    ];

    public function checkout(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Checkout');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Project', 'project_id');
    }

    public function shipping(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Shipping');
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Affiliate');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Customer');
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Delivery');
    }

    public function saleRefundHistory(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\SaleRefundHistory');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\User', 'owner_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function plansSales(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\PlanSale');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\Transaction');
    }

    public function notazzInvoices(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\NotazzInvoice');
    }

    public function productsPlansSale(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\ProductPlanSale');
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Gateway');
    }

    public function tracking(): HasMany
    {
        return $this->hasMany(Tracking::class);
    }

    public function saleWhiteBlackListResult(): HasMany
    {
        return $this->hasMany('Modules\Core\Entities\SaleWhiteBlackListResult');
    }

    public function saleLogs(): HasMany
    {
        return $this->hasMany(SaleLog::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function upsells(): HasMany
    {
        return $this->hasMany(Sale::class, 'upsell_id');
    }

    public function saleGatewayRequests(): HasMany
    {
        return $this->hasMany(SaleGatewayRequest::class);
    }

    public function contestations(): HasMany
    {
        return $this->hasMany(SaleContestation::class);
    }

    public function getnetChargebacks(): HasMany
    {
        return $this->hasMany(GetnetChargeback::class);
    }

    /**
     * @return HasMany
     */
    public function blockReasonsSale(): HasMany
    {
        return $this->hasMany(BlockReasonSale::class);
    }

    public function getHashIdAttribute()
    {
        return Hashids::connection('sale_id')->encode($this->id);
    }

    /**
     * @return BelongsTo
     */
    public function cashback()
    {
        return $this->hasOne(Cashback::class);
    }

    public function getValidTrackingForRedis(): int
    {

        $saleIsChargeback = $this->status == 4;
        $saleIsDigitalProduct = empty($this->delivery_id);
        $trackingNotRequired = !!$this->transactions
            ->where('tracking_required', false)
            ->where('type', Transaction::TYPE_PRODUCER)
            ->count();

        return $trackingNotRequired || $saleIsChargeback || $saleIsDigitalProduct ? 1 : (int)$this->has_valid_tracking;
    }

    /**
     * @return HasMany
     */
    public function pixCharges()
    {
        return $this->hasMany( PixCharge::class, 'sale_id');
    }
}
