<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

/**
 * @property integer $id
 * @property int $user_id
 * @property int $project_id
 * @property string $api_url
 * @property string $api_key
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Project $project
 * @property User $user
 */
class ActivecampaignIntegration extends Model
{
    use SoftDeletes, LogsActivity, HasFactory;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = ["user_id", "project_id", "api_url", "api_key", "created_at", "updated_at", "deleted_at"];

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
    public function project()
    {
        return $this->belongsTo("Modules\Core\Entities\Project");
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo("Modules\Core\Entities\User");
    }

    /**
     * @return HasMany
     */
    public function events()
    {
        return $this->hasMany("Modules\Core\Entities\ActivecampaignEvent");
    }

    /**
     * @return HasMany
     */
    public function customFields()
    {
        return $this->hasMany("Modules\Core\Entities\ActivecampaignCustom");
    }
}
