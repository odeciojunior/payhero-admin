<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\BlockReasonSalePresenter;

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

    /**
     * @var string
     */
    protected $presenter = BlockReasonSalePresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'blocked_reason_id',
        'status',
        'observation',
        'created_at',
        'updated_at',
    ];
    /**
     * @var bool
     */
    protected static $logFillable = true;
    /**
     * @var bool
     */
    protected static $logUnguarded = true;
    /**
     * Registra apenas os atributos alterados no log
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

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
