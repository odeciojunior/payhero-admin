<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $billing
 * @property string $celphone
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class SiteInvitationRequest extends Model
{
    use SoftDeletes, LogsActivity;
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'site_invitations_requests';
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'billing',
        'celphone',
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
}
