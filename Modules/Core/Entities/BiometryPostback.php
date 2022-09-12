<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property int $user_id
 * @property integer $user_biometry_resut_id
 * @property string $vendor
 * @property mixed $postback_data
 * @property mixed $api_data
 * @property boolean $processed_flag
 * @property string $date_api_data
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property UserBiometryResult $userBiometryResult
 * @property User $user
 */
class BiometryPostback extends Model
{
    use SoftDeletes;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'user_biometry_resut_id', 'vendor', 'postback_data', 'api_data', 'processed_flag', 'date_api_data', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userBiometryResult()
    {
        return $this->belongsTo('Modules\Core\Entities\UserBiometryResult', 'user_biometry_resut_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User');
    }
}
