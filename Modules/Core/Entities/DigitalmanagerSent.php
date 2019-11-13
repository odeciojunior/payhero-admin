<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

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
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'digitalmanager_sent';

    /**
     * The "type" of the auto-incrementing ID.
     * 
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
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function digitalmanagerIntegration()
    {
        return $this->belongsTo('Modules\Core\Entities\DigitalmanagerIntegration');
    }
}
