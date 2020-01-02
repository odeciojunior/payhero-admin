<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class UserDevice
 * @package Modules\Core\Entities
 */
class UserDevice extends Model
{
    use LogsActivity;

    //    protected $table = 'users_device';
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'player_id',
        'online',
        'identifier',
        'session_count',
        'language',
        'timezone',
        'game_version',
        'device_os',
        'device_type',
        'device_model',
        'ad_id',
        'tags',
        'last_active',
        'playtime',
        'amount_spent',
        'onsignal_created_ate',
        'invalid_identifier',
        'badge_count',
        'sdk',
        'test_type',
        'ip',
        'external_user_id',
        'sale_notification',
        'billet_notification',
        'payment_notification',
        'withdraw_notification',
        'invitation_sale_notification',
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
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }
}
