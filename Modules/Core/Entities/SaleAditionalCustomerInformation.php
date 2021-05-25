<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleAditionalCustomerInformation extends Model
{

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
