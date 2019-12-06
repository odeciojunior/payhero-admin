<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $whatsapp2_integration_id
 * @property string $data
 * @property string $response
 * @property int $sent_status
 * @property int $event_sale
 * @property integer $instance_id
 * @property string $instance
 * @property string $created_at
 * @property string $updated_at
 * @property Whatsapp2Integration $whatsapp2Integration
 */
class Whatsapp2Sent extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'whatsapp2_sent';

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
        'whatsapp2_integration_id',
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
    public function whatsapp2Integration()
    {
        return $this->belongsTo('Modules\Core\Entities\Whatsapp2Integration');
    }
}
