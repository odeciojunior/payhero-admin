<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\SalePresenter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Modules\Core\Entities\Sale
 *
 * @property int $id
 * @property int|null $owner_id
 * @property int|null $gateway_id
 * @property int|null $checkout_id
 * @property int|null $project_id
 * @property int|null $affiliate_id
 * @property int $customer_id
 * @property int|null $customer_card_id
 * @property int|null $delivery_id
 * @property int|null $shipping_id
 * @property int|null $upsell_id
 * @property int $attempts
 * @property string|null $payment_form
 * @property int|null $payment_method
 * @property string|null $total_paid_value
 * @property int|null $real_total_paid_value
 * @property int|null $recovery_discount_percent
 * @property int|null $original_total_paid_value
 * @property string|null $sub_total
 * @property string $shipment_value
 * @property string $start_date
 * @property string|null $end_date
 * @property string|null $date_refunded
 * @property string|null $gateway_transaction_id
 * @property string|null $gateway_order_id
 * @property string|null $flag
 * @property string|null $gateway_card_flag
 * @property int|null $status
 * @property string|null $gateway_status
 * @property string|null $gateway_tax_percent
 * @property int|null $gateway_tax_value
 * @property string|null $gateway_billet_identificator
 * @property int|null $installments_amount
 * @property int|null $real_installments_amount
 * @property string|null $installments_value
 * @property int|null $real_installments_value
 * @property string|null $installment_tax_value
 * @property string|null $boleto_link
 * @property string|null $boleto_digitable_line
 * @property string|null $boleto_due_date
 * @property string|null $cupom_code
 * @property string|null $shopify_order
 * @property string|null $woocommerce_order
 * @property string|null $nuvemshop_order
 * @property string|null $shopify_discount
 * @property string|null $dolar_quotation
 * @property int $first_confirmation
 * @property boolean $api_flag
 * @property int $automatic_discount
 * @property int|null $interest_total_value
 * @property int $refund_value
 * @property int $is_chargeback
 * @property int $is_chargeback_recovered
 * @property int $has_valid_tracking
 * @property int $has_order_bump
 * @property string|null $observation
 * @property string|null $antifraud_warning_level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Collection|SaleAdditionalCustomerInformation[] $additionalCustomerInformation
 * @property-read int|null $additional_customer_information_count
 * @property-read Affiliate|null $affiliate
 * @property-read Collection|BlockReasonSale[] $blockReasonsSale
 * @property-read int|null $block_reasons_sale_count
 * @property-read Cashback|null $cashback
 * @property-read Checkout|null $checkout
 * @property-read Collection|SaleContestation[] $contestations
 * @property-read int|null $contestations_count
 * @property-read Customer $customer
 * @property-read Delivery|null $delivery
 * @property-read Gateway|null $gateway
 * @property-read mixed $hash_id
 * @property-read string $id_code
 * @property-read Collection|NotazzInvoice[] $notazzInvoices
 * @property-read int|null $notazz_invoices_count
 * @property-read Collection|PixCharge[] $pixCharges
 * @property-read int|null $pix_charges_count
 * @property-read Collection|PlanSale[] $plansSales
 * @property-read int|null $plans_sales_count
 * @property-read Collection|ProductPlanSale[] $productsPlansSale
 * @property-read int|null $products_plans_sale_count
 * @property-read Project|null $project
 * @property-read Collection|SaleGatewayRequest[] $saleGatewayRequests
 * @property-read int|null $sale_gateway_requests_count
 * @property-read Collection|SaleLog[] $saleLogs
 * @property-read int|null $sale_logs_count
 * @property-read Collection|SaleRefundHistory[] $saleRefundHistory
 * @property-read int|null $sale_refund_history_count
 * @property-read Collection|SaleWhiteBlackListResult[] $saleWhiteBlackListResult
 * @property-read int|null $sale_white_black_list_result_count
 * @property-read Shipping|null $shipping
 * @property-read Collection|Ticket[] $tickets
 * @property-read int|null $tickets_count
 * @property-read Collection|Tracking[] $tracking
 * @property-read int|null $tracking_count
 * @property-read Collection|Tracking[] $trackings
 * @property-read int|null $trackings_count
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read Collection|Sale[] $upsells
 * @property-read int|null $upsells_count
 * @property-read User|null $user
 */
class Sale extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;
    use HasFactory;

    public const GETNET_SANDBOX_ID = 14;
    public const GETNET_PRODUCTION_ID = 15;
    public const GERENCIANET_PRODUCTION_ID = 18;

    public const CREDIT_CARD_PAYMENT = 1;
    public const BILLET_PAYMENT = 2;
    public const DEBIT_PAYMENT = 3;
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
    const PAYMENT_TYPE_PIX = 4;

    /**
     * @var string
     */
    protected $presenter = SalePresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "owner_id",
        "affiliate_id",
        "customer_id",
        "delivery_id",
        "shipping_id",
        "project_id",
        "checkout_id",
        "payment_form",
        "payment_method",
        "total_paid_value",
        "sub_total",
        "shipment_value",
        "start_date",
        "date_refunded",
        "end_date",
        "gateway_transaction_id",
        "gateway_order_id",
        "gateway_billet_identificator",
        "gateway_id",
        "status",
        "gateway_status",
        "upsell_id",
        "installments_amount",
        "installments_value",
        "flag",
        "boleto_link",
        "boleto_digitable_line",
        "boleto_due_date",
        "cupom_code",
        "shopify_order",
        "woocommerce_order",
        "nuvemshop_order",
        "shopify_discount",
        "dolar_quotation",
        "first_confirmation",
        "api_flag",
        "api_token_id",
        "installment_tax_value",
        "attempts",
        "created_at",
        "deleted_at",
        "updated_at",
        "gateway_card_flag",
        "gateway_tax_percent",
        "gateway_tax_value",
        "automatic_discount",
        "interest_total_value",
        "refund_value",
        "is_chargeback",
        "is_chargeback_recovered",
        "has_valid_tracking",
        "has_order_bump",
        "observation",
        "original_total_paid_value",
        "antifraud_warning_level",
        "antifraud_observation",
        "reportana_recovery_flag",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    public function checkout(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\Checkout");
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\Project", "project_id");
    }

    public function shipping(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\Shipping");
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\Affiliate");
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\Customer");
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\Delivery");
    }

    public function saleRefundHistory(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\SaleRefundHistory");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\User", "owner_id");
    }

    public function plansSales(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\PlanSale");
    }

    public function transactions(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\Transaction");
    }

    public function notazzInvoices(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\NotazzInvoice");
    }

    public function productsPlansSale(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\ProductPlanSale");
    }

    public function productsSaleApi(): HasMany
    {
        return $this->hasMany(ProductSaleApi::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo("Modules\Core\Entities\Gateway");
    }

    public function tracking(): HasMany
    {
        return $this->hasMany(Tracking::class);
    }

    public function saleWhiteBlackListResult(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\SaleWhiteBlackListResult");
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
        return $this->hasMany(Sale::class, "upsell_id");
    }

    public function saleGatewayRequests(): HasMany
    {
        return $this->hasMany(SaleGatewayRequest::class);
    }

    public function saleWoocommerceRequests(): HasMany
    {
        return $this->hasMany(SaleWoocommerceRequests::class);
    }

    public function contestations(): HasMany
    {
        return $this->hasMany(SaleContestation::class);
    }

    public function getnetChargebacks(): HasMany
    {
        return $this->hasMany(GetnetChargeback::class);
    }

    public function blockReasonsSale(): HasMany
    {
        return $this->hasMany(BlockReasonSale::class);
    }

    public function additionalCustomerInformation(): HasMany
    {
        return $this->hasMany("Modules\Core\Entities\SaleAdditionalCustomerInformation");
    }

    public function cashback(): HasOne
    {
        return $this->hasOne(Cashback::class);
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(Tracking::class);
    }

    public function pixCharges(): HasMany
    {
        return $this->hasMany(PixCharge::class, "sale_id");
    }

    /**
     * @return HasMany
     */
    public function saleInformations(): HasMany
    {
        return $this->hasMany(SaleInformation::class);
    }

    public function saleInformation(): HasOne
    {
        return $this->hasOne(SaleInformation::class);
    }

    /**
     * @return HasMany
     */
    public function pendingDebts(): HasMany
    {
        return $this->hasMany(PendingDebt::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, ProductPlanSale::class, "sale_id", "id", "id", "product_id");
    }

    public function apiToken(): BelongsTo
    {
        return $this->belongsTo(ApiToken::class);
    }

    public function getHashIdAttribute()
    {
        return Hashids::connection("sale_id")->encode($this->id);
    }

    public function getValidTrackingForRedis(): int
    {
        $saleIsChargeback = $this->status == 4;
        $saleIsDigitalProduct = empty($this->delivery_id);
        $trackingNotRequired = !!$this->transactions
            ->where("tracking_required", false)
            ->where("type", Transaction::TYPE_PRODUCER)
            ->count();

        return $trackingNotRequired || $saleIsChargeback || $saleIsDigitalProduct ? 1 : (int)$this->has_valid_tracking;
    }
}
