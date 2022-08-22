<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\DomainPresenter;
use Modules\Domains\Transformers\DomainResource;
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
 * @method DomainPresenter presenter()
 */
class Domain extends Model
{
    use FoxModelTrait;
    use LogsActivity;
    use PresentableTrait;
    use SoftDeletes;

    public const STATUS_PENDING = 1;
    public const STATUS_ANALYZING = 2;
    public const STATUS_APPROVED = 3;
    public const STATUS_REFUSED = 4;

    /**
     * @var string
     */
    protected $presenter = DomainPresenter::class;
    /**
     * @var array]
     */
    protected $dates = ["created_at", "updated_at", "deleted_at"];
    /**
     * @var array
     */
    protected $fillable = [
        "project_id",
        "cloudflare_domain_id",
        "name",
        "status",
        "sendgrid_id",
        "created_at",
        "updated_at",
        "deleted_at",
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
        switch ($eventName) {
            case "deleted":
                $activity->description = "Domínio {$this->name} foi deletedo.";
                break;
            case "updated":
                $activity->description = "Domínio {$this->name} foi atualizado.";
                break;
            case "created":
                $activity->description = "Domínio {$this->name} foi criado.";
                break;
            default:
                $activity->description = $eventName;
        }
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function domainsRecords(): HasMany
    {
        return $this->hasMany(DomainRecord::class);
    }
}
