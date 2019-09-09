<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\NotazzInvoicePresenter;

/**
 * @property integer $id
 * @property integer $sale_id
 * @property boolean $invoice_type
 * @property string $notazz_id
 * @property string $external_id
 * @property boolean $status
 * @property string $schedule
 * @property int $attempts
 * @property string $created_at
 * @property string $updated_at
 * @property Sale $sale
 */

/**
 * Class NotazzInvoice
 * @package App\Entities
 */
class NotazzInvoice extends Model
{
    use FoxModelTrait, SoftDeletes, PresentableTrait;
    /**
     * @var string
     */
    protected $presenter = NotazzInvoicePresenter::class;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
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
        'notazz_integration_id',
        'invoice_type',
        'notazz_id',
        'external_id',
        'status',
        'canceled_flag',
        'schedule',
        'attempts',
        'created_at',
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
