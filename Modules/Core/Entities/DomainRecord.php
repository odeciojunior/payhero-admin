<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property int $domain_id
 * @property string $cloudflare_record_id
 * @property string $type
 * @property string $name
 * @property string $content
 * @property boolean $system_flag
 * @property int $priority
 * @property string $created_at
 * @property string $updated_at
 * @property Domain $domain
 */
class DomainRecord extends Model
{
    use LogsActivity;
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'domains_records';
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'cloudflare_record_id',
        'type',
        'name',
        'content',
        'system_flag',
        'priority',
        'proxy',
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
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'deleted') {
            $activity->description = 'Entrada DNS ' . $this->name . ' foi deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Entrada DNS ' . $this->name . ' foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Entrada DNS ' . $this->name . ' foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo('Modules\Core\Entities\Domain');
    }
}
