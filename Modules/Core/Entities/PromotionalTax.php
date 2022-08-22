<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Modules\Core\Entities\PromotionalTax
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $expiration
 * @property string $tax
 * @property string|null $old_tax
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax newQuery()
 * @method static Builder|PromotionalTax onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax whereExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax whereOldTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionalTax whereUserId($value)
 * @method static Builder|PromotionalTax withTrashed()
 * @method static Builder|PromotionalTax withoutTrashed()
 */
class PromotionalTax extends Model
{
    use HasFactory;
    use SoftDeletes;

    const PROMOTIONAL_TAX = "3.9";

    protected $dates = ["expiration", "created_at", "updated_at"];

    protected $fillable = ["user_id", "expiration", "tax", "old_tax", "active"];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
