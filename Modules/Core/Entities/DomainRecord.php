<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

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
    protected $table = "domains_records";
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "domain_id",
        "cloudflare_record_id",
        "type",
        "name",
        "content",
        "system_flag",
        "priority",
        "proxy",
        "created_at",
        "updated_at",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logFillable()
            ->dontSubmitEmptyLogs();
    }

    /**
     * @return BelongsTo
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
