<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $digitalmanager_integration_id
 * @property string $data
 * @property string $response
 * @property int $sent_status
 * @property int $event_sale
 * @property integer $instance_id
 * @property string $instance
 * @property string $created_at
 * @property string $updated_at
 * @property DigitalmanagerIntegration $digitalmanagerIntegration
 */
class DigitalmanagerSent extends Model
{
    use LogsActivity;
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'digitalmanager_sent';
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'digitalmanager_integration_id',
        'data',
        'response',
        'sent_status',
        'event_sale',
        'instance_id',
        'instance',
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
    public function digitalmanagerIntegration()
    {
        return $this->belongsTo('Modules\Core\Entities\DigitalmanagerIntegration');
    }
}
