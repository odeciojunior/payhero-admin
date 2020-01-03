<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

/**
 * @property integer $id
 * @property string $nome
 * @property string $celular
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 */
class HubsmartInvitationRequest extends Model
{
    use LogsActivity;
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'hubsmart_invitation_request';
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'nome',
        'celular',
        'email',
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
}
