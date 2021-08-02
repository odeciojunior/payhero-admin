<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class GatewayPostback extends Model
{
    use HasFactory;
    use PresentableTrait;
    use SoftDeletes;

    protected $keyType = 'integer';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'sale_id',
        'gateway_id',
        'reference_id',
        'data',
        'gateway_enum',
        'gateway_postback_type',
        'gateway_status',
        'gateway_payment_type',
        'description',
        'amount',
        'processed_flag',
        'postback_valid_flag',
        'pay_postback_flag',
        'machine_result',
        'created_at',
        'updated_at',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
