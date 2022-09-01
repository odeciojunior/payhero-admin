<?php

namespace Modules\Core\Entities;

use App\Traits\FoxModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\DomainPresenter;
use Modules\Domains\Transformers\DomainResource;
use Spatie\Activitylog\LogOptions;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
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
