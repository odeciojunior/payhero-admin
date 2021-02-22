<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $id
 * @property string $name
 * @property integer $level
 * @property User $users
 * @property string $created_at
 * @property string $updated_at
 */
class Task extends Model
{
    const TASK_APPROVED_DOCS      = 1;
    const TASK_CREATE_FIRST_STORE = 2;
    const TASK_FIRST_SALE         = 3;
    const TASK_FIRST_1000_REVENUE = 4;
    const TASK_500_SALES          = 5;
    const TASK_5_INVITATIONS      = 6;

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
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'tasks_users',
            'task_id',
            'user_id'
        );
    }
}
