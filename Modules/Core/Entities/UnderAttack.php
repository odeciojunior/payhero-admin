<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modules\Core\Entities\UnderAttack
 *
 * @property int $id
 * @property int|null $domain_id
 * @property int|null $user_id
 * @property string $type
 * @property string|null $percentage_card_refused
 * @property string|null $start_date_card_refused
 * @property string|null $end_date_card_refused
 * @property string|null $total_refused
 * @property string|null $removed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Domain|null $domain
 * @property-read Domain|null $domainWithTrashed
 * @method static Builder|UnderAttack newModelQuery()
 * @method static Builder|UnderAttack newQuery()
 * @method static Builder|UnderAttack query()
 * @method static Builder|UnderAttack whereCreatedAt($value)
 * @method static Builder|UnderAttack whereDomainId($value)
 * @method static Builder|UnderAttack whereEndDateCardRefused($value)
 * @method static Builder|UnderAttack whereId($value)
 * @method static Builder|UnderAttack wherePercentageCardRefused($value)
 * @method static Builder|UnderAttack whereRemovedAt($value)
 * @method static Builder|UnderAttack whereStartDateCardRefused($value)
 * @method static Builder|UnderAttack whereTotalRefused($value)
 * @method static Builder|UnderAttack whereType($value)
 * @method static Builder|UnderAttack whereUpdatedAt($value)
 * @method static Builder|UnderAttack whereUserId($value)
 */
class UnderAttack extends Model
{
    use HasFactory;

    const MAX_ATTEMPT = 20;

    protected $dates = ["created_at", "updated_at"];

    protected $fillable = [
        "domain_id",
        "user_id",
        "type",
        "percentage_card_refused",
        "start_date_card_refused",
        "end_date_card_refused",
        "total_refused",
        "removed_at",
        "created_at",
        "updated_at",
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function domainWithTrashed()
    {
        return $this->belongsTo(Domain::class, "domain_id", "id")->withTrashed();
    }
}
