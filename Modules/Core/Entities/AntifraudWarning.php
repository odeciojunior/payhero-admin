<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modules\Core\Entities\AntifraudWarning
 *
 * @property int $id
 * @property int|null $sale_id
 * @property int $status
 * @property string $column
 * @property string $value
 * @property string $level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Sale|null $sale
 * @method static Builder|AntifraudWarning newModelQuery()
 * @method static Builder|AntifraudWarning newQuery()
 * @method static Builder|AntifraudWarning query()
 * @method static Builder|AntifraudWarning whereColumn($value)
 * @method static Builder|AntifraudWarning whereCreatedAt($value)
 * @method static Builder|AntifraudWarning whereId($value)
 * @method static Builder|AntifraudWarning whereLevel($value)
 * @method static Builder|AntifraudWarning whereSaleId($value)
 * @method static Builder|AntifraudWarning whereStatus($value)
 * @method static Builder|AntifraudWarning whereUpdatedAt($value)
 * @method static Builder|AntifraudWarning whereValue($value)
 */
class AntifraudWarning extends Model
{
    use HasFactory;

    public const STATUS_FRAUD_CONFIRMED = 1;
    public const STATUS_FRAUD_WARNING = 2;

    protected $keyType = "integer";

    protected $fillable = ["sale_id", "status", "column", "value", "level"];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
