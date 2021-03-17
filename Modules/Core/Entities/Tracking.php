<?php

namespace Modules\Core\Entities;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TrackingPresenter;
use Spatie\Activitylog\Models\Activity;

/**
 * @property int $product_plan_sale_id
 * @property int $sale_id
 * @property int $product_id
 * @property int $amount
 * @property int $delivery_id
 * @property string $tracking_code
 * @property int $tracking_status_enum
 * @property int $system_status_enum
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 * @property Delivery $delivery
 * @property ProductPlanSale $productPlanSale
 * @property Sale $sale
 * @property Product $product
 * @method TrackingPresenter present()
 */
class Tracking extends Model
{
    use PresentableTrait, LogsActivity;

    /** Tracking Status */
    const STATUS_POSTED           = 1;
    const STATUS_DISPATCHED       = 2;
    const STATUS_DELIVERED        = 3;
    const STATUS_OUT_FOR_DELIVERY = 4;
    const STATUS_EXCEPTION        = 5;

    /** System Status */
    const SYSTEM_STATUS_VALID              = 1; // O código passou em todas as validações
    const SYSTEM_STATUS_NO_TRACKING_INFO   = 2; // O código é reconhecido pela transportadora mas ainda não tem nenhuma movimentação
    const SYSTEM_STATUS_UNKNOWN_CARRIERr   = 3; // O código não foi reconhecido por nenhuma transportadora
    const SYSTEM_STATUS_POSTED_BEFORE_SALE = 4; // A data de postagem da remessa é anterior a data da venda
    const SYSTEM_STATUS_DUPLICATED         = 5; // Já existe uma venda com esse código de rastreio cadastrado
    const SYSTEM_STATUS_CHECKED_MANUALLY   = 7; // Código de rastreio verificado manualmente (no Manager)

    /**
     * @var string
     */
    protected $presenter = TrackingPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'product_plan_sale_id',
        'sale_id',
        'product_id',
        'amount',
        'delivery_id',
        'tracking_code',
        'tracking_status_enum',
        'system_status_enum',
        'created_at',
        'updated_at',
        'deleted_at',
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
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'Código rastreio foi deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Código de rastreio foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Código de rastreio foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * @return BelongsTo
     */
    public function productPlanSale()
    {
        return $this->belongsTo(ProductPlanSale::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
