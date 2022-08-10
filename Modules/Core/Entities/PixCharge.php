<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PixCharge extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        "sale_id",
        "gateway_id",
        "txid",
        "e2eId",
        "location_id",
        "location",
        "qrcode",
        "qrcode_image",
        "status",
        "expiration_date",
        "created_at",
        "updated_at",
    ];

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * @return BelongsTo
     */
    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }
}
