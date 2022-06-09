<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

/**
 * @property integer $id
 * @property int $manager_user_id
 * @property int $sirius_user_id
 * @property boolean $is_active
 * @property string $token
 * @property string $created_at
 * @property string $updated_at
 * @property UserMaganer $userManager
 * @property UserSirius $userSirius
 */
class ManagerToSiriusLogin extends Model
{
    //use SoftDeletes, LogsActivity;
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['manager_user_id', 'sirius_user_id', 'is_active', 'token', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userManager()
    {
        return $this->belongsTo('ModulesCoreEntities\User', 'manager_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userSirius()
    {
        return $this->belongsTo('ModulesCoreEntities\User', 'sirius_user_id');
    }
}
