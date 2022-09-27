<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Entities\User;

/**
 * @property int $id
 * @property int $user_id
 * @property string $vendor
 * @property string $biometry_id
 * @property string $score
 * @property string $status
 * @property string $request_data
 * @property string $response_data
 * @property string $postback_data
 * @property string $api_data
 * @property User $user
 */
class UserBiometryResult extends Model
{
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
        'vendor',
        'biometry_id',
        'score',
        'status',
        'request_data',
        'response_data',
        'created_at',
        'updated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
