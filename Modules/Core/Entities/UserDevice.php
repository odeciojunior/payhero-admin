<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class UserDevice
 * @package Modules\Core\Entities
 */
class UserDevice extends Model
{
    use LogsActivity;

    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = "integer";
    /**
     * @var array
     */
    protected $fillable = [
        "user_id",
        "player_id",
        "online",
        "identifier",
        "session_count",
        "language",
        "timezone",
        "game_version",
        "device_os",
        "device_type",
        "device_model",
        "ad_id",
        "tags",
        "last_active",
        "playtime",
        "amount_spent",
        "onsignal_created_ate",
        "invalid_identifier",
        "badge_count",
        "sdk",
        "test_type",
        "ip",
        "external_user_id",
        "sale_notification",
        "billet_notification",
        "payment_notification",
        "withdraw_notification",
        "invitation_sale_notification",
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
    public function user()
    {
        return $this->belongsTo("Modules\Core\Entities\User");
    }
}
