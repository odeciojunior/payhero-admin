<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Presenters\InvitePresenter;

/**
 * @property integer $id
 * @property int $invite
 * @property int $user_invited
 * @property int $company_id
 * @property int $invitation_id
 * @property string $email_invited
 * @property int $status
 * @property string $register_date
 * @property string $expiration_date
 * @property string $parameter
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Company $company
 * @property Invitation $invitation
 * @property User $user
 */
class Invitation extends Model
{

    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = InvitePresenter::class;

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
        'company_id',
        'invitation_id',
        'email_invited',
        'status',
        'register_date',
        'expiration_date',
        'parameter',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('Modules\Core\Entities\Company', 'company_id');
    }

    /**
     * @return BelongsTo
     */
    public function userInvited()
    {
        return $this->belongsTo('Modules\Core\Entities\User', 'user_invited');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Core\Entities\User', 'invite');
    }

    /**
     * @return BelongsTo
     */
    public function invitation()
    {
        return $this->belongsTo('Modules\Core\Entities\Invitation');
    }
}
