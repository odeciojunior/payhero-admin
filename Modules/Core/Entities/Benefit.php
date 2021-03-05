<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property integer $id
 * @property string $name
 * @property integer $level
 * @property User $user
 * @property string $created_at
 * @property string $updated_at
 */
class Benefit extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'level',
        'description',
        'created_at',
        'updated_at'
    ];

    /**
     * @return HasManyThrough
     */
    public function users()
    {
        return $this->hasManyThrough(User::class, UserBenefit::class, 'benefit_id', 'id', 'id', 'user_id');
    }
}
