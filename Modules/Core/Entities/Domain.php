<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\DomainPresenter;
use Modules\Domains\Transformers\DomainResource;
use App\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

/**
 * Class Domain
 * @package Modules\Core\Entities
 * @property int $id
 * @property int $project_id
 * @property string $cloudflare_domain_id
 * @property string $name
 * @property int $status
 * @property string $sendgrid_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property DomainResource[] $domainsRecords
 */
class Domain extends Model
{
    use SoftDeletes, PresentableTrait, FoxModelTrait, LogsActivity;
    /**
     * @var string
     */
    protected $presenter = DomainPresenter::class;
    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * @var array
     */
    protected $fillable = [
        'project_id',
        'cloudflare_domain_id',
        'name',
        'status',
        'sendgrid_id',
        'created_at',
        'updated_at',
        'deleted_at',
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
            $activity->description = 'Domínio ' . $this->name . ' foi deletedo.';
        } else if ($eventName == 'updated') {
            $activity->description = 'Domínio ' . $this->name . ' foi atualizado.';
        } else if ($eventName == 'created') {
            $activity->description = 'Domínio ' . $this->name . ' foi criado.';
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
    public function domainsRecords()
    {
        return $this->hasMany('Modules\Core\Entities\DomainRecord');
    }
}
