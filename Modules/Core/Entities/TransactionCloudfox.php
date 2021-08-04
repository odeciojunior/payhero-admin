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
 * @property int $type
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
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_cloudfox';

    protected $presenter = TransactionPresenter::class;

    protected $keyType = 'integer';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'gateway_id',
        'company_id',
        'user_id',
        'value',
        'type',
        'status',
        'status_enum',
        'release_date',
        'gateway_released_at',
        'gateway_transferred_at'
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
