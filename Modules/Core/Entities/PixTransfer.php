<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

class PixTransfer extends Model
{
    use FoxModelTrait;
    use PresentableTrait;

    const STATUS_PROCESSING = "PROCESSING";
    const STATUS_REALIZED = "REALIZED";
    const STATUS_UNREALIZED = "UNREALIZED";

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";

    protected $dates = ["created_at", "updated_at"];

    protected $fillable = [
        "withdrawal_id",
        "pix_transaction_id",
        "value",
        "requested_in",
        "latest_status_updated",
        "transaction_ids",
        "status",
        "postback",
    ];
}
