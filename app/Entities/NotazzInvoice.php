<?php

namespace App\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
class NotazzInvoice extends Model
{
    use FoxModelTrait;
    use SoftDeletes;
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
     * @var array
     */
    private $enum = [
        'status' => [
            1 => 'pending',
            2 => 'analyzing',
            3 => 'approved',
            4 => 'refused',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('App\Entities\Sale');
    }
}
