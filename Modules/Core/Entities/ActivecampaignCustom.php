<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property integer $activecampaign_integration_id
 * @property string $custom_field
 * @property int $custom_field_id
 * @property string $created_at
 * @property string $updated_at
 * @property ActivecampaignIntegration $activecampaignIntegration
 */
class ActivecampaignCustom extends Model
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
        'activecampaign_integration_id',
        'custom_field',
        'custom_field_id',
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
            $activity->description = 'Evento foi deletado para ActivecampaignCustom';
        } else if ($eventName == 'updated') {
            $activity->description = 'Evento foi atualizado para ActivecampaignCustom';
        } else if ($eventName == 'created') {
            $activity->description = 'Evento foi criado para ActivecampaignCustom';
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
}
