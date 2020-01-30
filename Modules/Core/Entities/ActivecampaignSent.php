<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Activity;
use App\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $instance_id
 * @property string $instance
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
    use LogsActivity;
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'activecampaign_sent';
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'instance_id',
        'instance',
        'activecampaign_integration_id',
        'data',
        'response',
        'sent_status',
        'event_sale',
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
     * Registra apenas os atributos alterados
     * @var bool
     */
    protected static $logOnlyDirty = true;
    /**
     * Impede que o pacote armazene logs vazios
     * @var bool
     */
    protected static $submitEmptyLogs = false;

    /**
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'ActivecampaignSent foi deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'ActivecampaignSent foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'ActivecampaignSent foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function activecampaignIntegration()
    {
        return $this->belongsTo('Modules\Core\Entities\ActivecampaignIntegration');
    }

    /**
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('Modules\Core\Entities\Sale');
    }
}
