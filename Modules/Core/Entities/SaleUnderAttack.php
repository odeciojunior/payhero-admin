<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modules\Core\Entities\SaleUnderAttack
 *
 * @property int $id
 * @property int $under_attack_id
 * @property int $sale_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Sale $sale
 * @property-read UnderAttack $underAttack
 * @method static Builder|SaleUnderAttack newModelQuery()
 * @method static Builder|SaleUnderAttack newQuery()
 * @method static Builder|SaleUnderAttack query()
 * @method static Builder|SaleUnderAttack whereCreatedAt($value)
 * @method static Builder|SaleUnderAttack whereId($value)
 * @method static Builder|SaleUnderAttack whereSaleId($value)
 * @method static Builder|SaleUnderAttack whereUnderAttackId($value)
 * @method static Builder|SaleUnderAttack whereUpdatedAt($value)
 */
class SaleUnderAttack extends Model
{
    use HasFactory;

    protected $dates = ["created_at", "updated_at"];

    protected $fillable = ["sale_id", "under_attack_id", "created_at", "updated_at"];

    public function underAttack(): BelongsTo
    {
        return $this->belongsTo(UnderAttack::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
