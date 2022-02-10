<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property mixed $sent_data
 * @property mixed $response
 * @property string $created_at
 * @property string $updated_at
 */
class GatewaysBackofficeRequest extends Model
{
    protected $keyType = 'integer';

    protected $fillable = [
        'company_id',
        'gateway_id',
        'sent_data',
        'response',
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('Modules\Core\Entities\Company');
    }

    /**
     * @return BelongsTo
     */
    public function gateway(): BelongsTo
    {
        return $this->belongsTo('Modules\Core\Entities\Gateway');
    }
}
