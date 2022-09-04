<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\DeliveryPresenter;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property integer $customer_id
 * @property string $receiver_name
 * @property string $zip_code
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $neighborhood
 * @property string $street
 * @property string $number
 * @property string $complement
 * @property string $type
 * @property integer $melhorenvio_carrier_id
 * @property string $melhorenvio_order_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Sale[] $sales
 * @property Customer $customer
 */
class Delivery extends Model
{
    use PresentableTrait, SoftDeletes, FoxModelTrait, LogsActivity, HasFactory;
    /**
     * @var string
     */
    protected $presenter = DeliveryPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "customer_id",
        "receiver_name",
        "zip_code",
        "country",
        "state",
        "city",
        "neighborhood",
        "street",
        "number",
        "complement",
        "type",
        "melhorenvio_carrier_id",
        "melhorenvio_order_id",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return BelongsTo
     */
    public function carrier()
    {
        return $this->belongsTo("Modules\Core\Entities\Carrier");
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany("Modules\Core\Entities\Sale");
    }

    /**
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
