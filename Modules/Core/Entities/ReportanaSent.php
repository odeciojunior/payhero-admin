<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

/**
 * @property integer $id
 * @property integer $reportana_integration_id
 * @property string $data
 * @property string $response
 * @property int $sent_status
 * @property int $event_sale
 * @property integer $instance_id
 * @property string $instance
 * @property string $created_at
 * @property string $updated_at
 * @property ReportanaIntegration $reportanaIntegration
 */
class ReportanaSent extends Model
{
    use LogsActivity;
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'reportana_sent';
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'reportana_integration_id',
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
     * @return BelongsTo
     */
    public function reportanaIntegration()
    {
        return $this->belongsTo(ReportanaIntegration::class);
    }
}
