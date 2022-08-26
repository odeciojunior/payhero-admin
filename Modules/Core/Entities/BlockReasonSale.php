<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\BlockReasonSalePresenter;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property int $sale_id
 * @property int $blocked_reason_id
 * @property string $observation
 * @property tinyinteger $status
 * @property string $created_at
 * @property string $updated_at
 * @property Sale[] $sales
 * @property BlockReason[] $blockReasons
 */
class BlockReasonSale extends Model
{
    use LogsActivity;
    use PresentableTrait;

    public const STATUS_BLOCKED = 1;
    public const STATUS_UNLOCKED = 2;
    public const STATUS_PENDING_BLOCK = 3;

    public const BLOCK_REASON_ID_TICKET = 8;

    /**
     * @var string
     */
    protected $presenter = BlockReasonSalePresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = ["sale_id", "blocked_reason_id", "status", "observation", "created_at", "updated_at"];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function sale()
    {
        return $this->BelongsTo(Sale::class);
    }

    /**
     * @return BelongsTo
     */
    public function blockReason()
    {
        return $this->BelongsTo(BlockReason::class);
    }
}
