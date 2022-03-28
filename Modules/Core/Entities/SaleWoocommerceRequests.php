<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SaleWoocommerceRequests extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'project_id',
        'method',
        'status',
        'send_data',
        'received_data',
        'created_at',
        'updated_at'
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

}
