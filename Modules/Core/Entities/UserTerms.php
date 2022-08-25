<?php

namespace Modules\Core\Entities;

use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

/**
 * Class UserTerms
 * @package Modules\Core\Entities
 */
class UserTerms extends Model
{
    use SoftDeletes, LogsActivity;

    /**
     * @var array
     */
    protected $dates = ["accepted_at", "created_at", "update_at", "deleted_at"];
    /**
     * @var array
     */
    protected $fillable = ["user_id", "term_version", "device_data", "accepted_at"];

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
    public function user()
    {
        return $this->belongsTo("Modules\Core\Entities\User");
    }
}
