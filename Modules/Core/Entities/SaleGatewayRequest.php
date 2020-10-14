<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleGatewayRequest extends Model
{

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
