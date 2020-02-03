<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $client_id
 * @property mixed $send_dataxi
 * @property mixed $received_data
 * @property mixed $exception
 * @property string $created_at
 * @property string $updated_at
 * @property Client $client
 */
class ClientIdwallResult extends Model
{
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
        'client_id',
        'send_data',
        'received_data',
        'exception',
        'created_at',
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('Modules\Core\Entities\Client');
    }
}
