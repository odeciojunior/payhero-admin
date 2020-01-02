<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property boolean $card_flag_enum
 * @property boolean $active_flag
 * @property string $created_at
 * @property string $updated_at
 * @property GatewayFlagTax[] $gatewayFlagTaxes
 * @property Gateway $gateway
 */
class GatewayFlag extends Model
{
    use LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'card_flag_enum',
        'active_flag',
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
    public function gatewayFlagTaxes()
    {
        return $this->hasMany('Modules\Core\Entities\GatewayFlagTax');
    }

    /**
     * @return BelongsTo
     */
    public function gateway()
    {
        return $this->belongsTo('Modules\Core\Entities\Gateway');
    }
}
