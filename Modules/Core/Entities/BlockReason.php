<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Traits\LogsActivity;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\BlockReasonPresenter;

/**
 * @property integer $id
 * @property string $reason
 * @property tinyinteger $reason_enum
 * @property string $created_at
 * @property string $updated_at
 * @property Campaign[] $blockReasonsSales
 * @property ClientsCookie[] $clientsCookies
 * @property Sale[] $sales
 */
class BlockReason extends Model
{
    use LogsActivity, PresentableTrait;
    /**
     * @var string
     */
    public const IN_DISPUTE = 1;
    
    protected $presenter = BlockReasonPresenter::class;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'reason',
        'reason_enum',
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
     * @return BelongsTo
     */
    public function blockReasonsSales()
    {
        return $this->belongsTo(BlockReasonSale::class);
    }

    /**
     * @return HasManyThrough
     */
    public function sales()
    {
        return $this->hasManyThrough(BlockReasonSale::class,Sale::class);
    }
}
