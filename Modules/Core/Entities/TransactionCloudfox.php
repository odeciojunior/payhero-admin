<?php

namespace Modules\Core\Entities;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\TransactionPresenter;

/**
 * @property integer $id
 * @property integer $sale_id
 * @property integer $gateway_id
 * @property int $company_id
 * @property int $user_id
 * @property string $value
 * @property string $value_total
 * @property string $status
 * @property int $status_enum
 * @property string $release_date
 * @property string $gateway_released_at
 * @property string $gateway_transferred_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Company $company
 * @property Gateway $gateway
 * @property Sale $sale
 * @property User $user
 */
class TransactionCloudfox extends Model
{
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;

    const STATUS_TRANSFERRED = 1;
    const STATUS_PAID = 2;
    const STATUS_PENDING = 3;
    const STATUS_CHARGEBACK = 4;
    const STATUS_CANCELED = 5;
    const STATUS_REFUNDED = 6;
    const STATUS_REFUSED = 7;
    const STATUS_PENDING_ANTIFRAUD = 8;
    const STATUS_CANCELED_ANTIFRAUD = 9;
    const STATUS_WAITING_WITHDRAWAL = 10;
    const STATUS_ANTICIPATED = 12;
    const STATUS_BILLET_REFUNDED = 13;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "transaction_cloudfox";

    protected $presenter = TransactionPresenter::class;

    protected $keyType = "integer";

    protected $dates = ["created_at", "updated_at", "deleted_at"];

    /**
     * @var array
     */
    protected $fillable = [
        "sale_id",
        "gateway_id",
        "company_id",
        "user_id",
        "value",
        "value_total",
        "status",
        "status_enum",
        "release_date",
        "gateway_released_at",
        "gateway_transferred_at",
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
