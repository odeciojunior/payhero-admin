<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\SalePresenter;

/**
 * @property integer $id
 * @property int $sale_id
 * @property int $refunded_amount
 * @property DateTime $date_refunded
 * @property string $gateway_response
 * @property string $created_at
 * @property string $deleted_at
 * @property string $updated_at
 * @property Sale $sale
 */
class SaleRefundHistory extends Model
{
    use FoxModelTrait, SoftDeletes, PresentableTrait;
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
        'sale_id',
        'refunded_amount',
        'date_refunded',
        'gateway_response',
        'created_at',
        'deleted_at',
        'updated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('Modules\Core\Entities\Sale');
    }
}
