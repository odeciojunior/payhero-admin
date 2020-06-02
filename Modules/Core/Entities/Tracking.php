<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TrackingPresenter;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property integer $product_plan_sale_id
 * @property integer $delivery_id
 * @property string $tracking_code
 * @property boolean $tracking_type_enum
 * @property int $tracking_status_enum
 * @property int $system_status_enum
 * @property string $tracking_date
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Delivery $delivery
 * @property ProductPlanSale $productPlanSale
 * @method TrackingPresenter present()
 */
class Tracking extends Model
{
    use PresentableTrait, LogsActivity;
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
