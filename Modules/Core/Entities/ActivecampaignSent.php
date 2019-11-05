<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $sale_id
 * @property integer $activecampaign_integration_id
 * @property string $data
 * @property string $response
 * @property int $sent_status
 * @property string $created_at
 * @property string $updated_at
 * @property ActivecampaignIntegration $activecampaignIntegration
 * @property Sale $sale
 */
class ActivecampaignSent extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'activecampaign_sent';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['sale_id', 'activecampaign_integration_id', 'data', 'response', 'sent_status', 'event_sale', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activecampaignIntegration()
    {
        return $this->belongsTo('Modules\Core\Entities\ActivecampaignIntegration');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('Modules\Core\Entities\Sale');
    }
}
