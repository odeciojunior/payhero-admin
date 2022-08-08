<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modules\Core\Entities\NethoneAntifraudTransaction
 *
 * @property int $id
 * @property int $sale_id
 * @property string|null $inquiry_id
 * @property int|null $transaction_id
 * @property mixed|null $result
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Sale $sale
 */
class NethoneAntifraudTransaction extends Model
{
    use HasFactory;

    protected $table = "nethone_antifraud_transaction";

    protected $keyType = "integer";

    protected $fillable = ["sale_id", "transaction_id", "result", "created_at", "updated_at"];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
