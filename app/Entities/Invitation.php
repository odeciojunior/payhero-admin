<?php

namespace App\Entities;

use App\Traits\ModelEnumTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $invite
 * @property int $user_invited
 * @property int $company
 * @property string $email_invited
 * @property string $status
 * @property string $register_date
 * @property string $expiration_date
 * @property string $parameter
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Company $company
 * @property User $user
 * @property User $user
 */
class Invitation extends Model
{
    use ModelEnumTrait;
    /**
     * The "type" of the auto-incrementing ID.
     * @var string
     */
    protected $keyType = 'integer';
    /**
     * @var array
     */
    protected $fillable = [
        'invite',
        'user_invited',
        'company',
        'email_invited',
        'status',
        'register_date',
        'expiration_date',
        'parameter',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    private $enum = [
        'status' => [
            1 => 'sent_invite',
        ],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Entities\Company', 'company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Entities\User', 'user_invited');
    }
}
