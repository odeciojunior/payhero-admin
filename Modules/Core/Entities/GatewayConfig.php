<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $gateway_id
 * @property string $name 
 * @property string $created_at
 * @property string $updated_at
 * @property Gateway $gateway
 */
class GatewayConfig extends Model
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
        'gateway_id',
        'type',
        'type_enum',
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
    public function gateway()
    {
        return $this->belongsTo('Modules\Core\Entities\Gateway');
    }
}
