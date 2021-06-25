<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\ShippingPresenter;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * @property integer $id
 * @property int $project_id
 * @property string $name
 * @property string $information
 * @property string $value
 * @property string $type
 * @property string $zip_code_origin
 * @property integer|null $melhorenvio_integration_id
 * @property boolean $receipt
 * @property boolean $own_hand
 * @property boolean $status
 * @property boolean $pre_selected
 * @property string $apply_on_plans
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property Collection $sales
 * @property MelhorenvioIntegration $melhorenvioIntegration
 */
class Shipping extends Model
{
    use SoftDeletes, FoxModelTrait, PresentableTrait, LogsActivity;

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $appends = ['id_code'];
    /**
     * @var string
     */
    protected $presenter = ShippingPresenter::class;
    /**
     * @var array
     */
    protected $fillable = [
        'project_id',
        'name',
        'information',
        'value',
        'type',
        'type_enum',
        'zip_code_origin',
        'melhorenvio_integration_id',
        'receipt',
        'own_hand',
        'status',
        'rule_value',
        'pre_selected',
        'apply_on_plans',
        'not_apply_on_plans',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $attributes = [
        'apply_on_plans' => ['all'],
        'not_apply_on_plans' => []
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
            $activity->description = 'Frete ' . $this->name . ' foi deletado.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Frete ' . $this->name . ' foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Frete ' . $this->name . ' foi criado.';
        } else {
            $activity->description = $eventName;
        }
    }

    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Modules\Core\Entities\Project');
    }

    /**
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany('Modules\Core\Entities\Sale');
    }

    /**
     * @return BelongsTo
     */
    public function melhorenvioIntegration()
    {
        return $this->belongsTo(MelhorenvioIntegration::class);
    }
}
