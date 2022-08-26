<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

/**
 * Modules\Core\Entities\SaleLog
 *
 * @property int $id
 * @property int $sale_id
 * @property int $status_enum
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Sale $sale
 * @method static Builder|SaleLog newModelQuery()
 * @method static Builder|SaleLog newQuery()
 * @method static Builder|SaleLog query()
 * @method static Builder|SaleLog whereCreatedAt($value)
 * @method static Builder|SaleLog whereId($value)
 * @method static Builder|SaleLog whereSaleId($value)
 * @method static Builder|SaleLog whereStatus($value)
 * @method static Builder|SaleLog whereStatusEnum($value)
 * @method static Builder|SaleLog whereUpdatedAt($value)
 */
class SaleLog extends Model
{
    use SoftDeletes;

    protected $table = "sale_logs";

    protected $keyType = "integer";

    protected $fillable = ["sale_id", "status_enum", "status", "created_at", "updated_at"];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
